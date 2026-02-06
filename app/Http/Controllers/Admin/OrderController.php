<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items.artwork')
            ->latest();

        // Silinmişleri göster
        if ($request->has('with_trashed')) {
            $query->withTrashed();
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.artwork.artist');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,confirmed,shipped,delivered,cancelled,payment_failed',
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        // Onaylanma durumunda özel işlemler (pending veya paid'den geçişte)
        if ($newStatus === 'confirmed' && !in_array($previousStatus, ['confirmed', 'shipped', 'delivered'])) {
            return $this->confirmOrder($order);
        }

        // İptal durumunda eserleri serbest bırak
        if ($newStatus === 'cancelled' && $previousStatus !== 'cancelled') {
            return $this->cancelOrder($order);
        }

        $order->update($validated);

        return redirect()->back()
            ->with('success', 'Sipariş durumu güncellendi.');
    }

    /**
     * Siparişi onayla - ArtPuan dağıt, bildirim gönder
     */
    protected function confirmOrder(Order $order)
    {
        DB::beginTransaction();

        try {
            // Siparişi onayla
            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Eserleri satıldı olarak işaretle
            foreach ($order->items as $item) {
                if ($item->artwork) {
                    $item->artwork->update([
                        'is_sold' => true,
                        'is_reserved' => false,
                    ]);
                }
            }

            $buyer = $order->user;
            $notificationService = new NotificationService();

            if ($buyer) {
                // Alıcıya %1 ArtPuan
                $buyerPuan = round($order->total_tl * 0.01, 2);
                if ($buyerPuan > 0) {
                    $buyer->addArtPuan($buyerPuan, [
                        'type' => 'purchase',
                        'order_id' => $order->id,
                        'description' => "#{$order->order_number} nolu sipariş onayı - %1 ArtPuan",
                    ]);

                    // Alıcıya bildirim
                    try {
                        $notificationService->notifyBuyerArtPuan($buyer, $buyerPuan, $order);
                    } catch (\Exception $e) {
                        Log::error('Alici ArtPuan bildirim hatasi: ' . $e->getMessage());
                    }
                }

                // Referans sahibine %1 ArtPuan
                if ($buyer->referred_by) {
                    $referrer = User::find($buyer->referred_by);
                    if ($referrer) {
                        $referrerPuan = round($order->total_tl * 0.01, 2);
                        if ($referrerPuan > 0) {
                            $referrer->addArtPuan($referrerPuan, [
                                'type' => 'referral',
                                'order_id' => $order->id,
                                'source_user_id' => $buyer->id,
                                'description' => "{$buyer->name} referansı - #{$order->order_number} siparişi",
                            ]);

                            // Referans sahibine bildirim
                            try {
                                $notificationService->notifyReferrerArtPuan($referrer, $buyer, $referrerPuan, $order);
                            } catch (\Exception $e) {
                                Log::error('Referans ArtPuan bildirim hatasi: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Sipariş onaylandı! ArtPuan dağıtımı ve bildirimler gönderildi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Siparis onaylama hatasi: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Sipariş onaylanırken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Siparişi soft delete ile sil
     */
    public function destroy(Order $order)
    {
        DB::beginTransaction();

        try {
            // Önce siparişi iptal et (eserler serbest bırakılsın)
            if (!in_array($order->status, ['cancelled'])) {
                $order->update(['status' => 'cancelled']);

                // Eserleri serbest bırak
                foreach ($order->items as $item) {
                    if ($item->artwork) {
                        $item->artwork->update([
                            'is_reserved' => false,
                            'is_sold' => false,
                        ]);
                    }
                }
            }

            // İlişkili kayıtları soft delete yap
            $order->items()->delete();
            $order->paymentTransactions()->delete();

            // Siparişi soft delete yap
            $order->delete();

            Log::info('Order deleted', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'deleted_by' => auth()->user()->name ?? 'Admin',
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', "#{$order->order_number} nolu sipariş silindi.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order delete error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Sipariş silinirken bir hata oluştu.');
        }
    }

    /**
     * Silinmiş siparişi geri yükle
     */
    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::withTrashed()->findOrFail($id);

            // Siparişi geri yükle
            $order->restore();

            // İlişkili kayıtları geri yükle
            $order->items()->withTrashed()->restore();
            $order->paymentTransactions()->withTrashed()->restore();

            Log::info('Order restored', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'restored_by' => auth()->user()->name ?? 'Admin',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', "#{$order->order_number} nolu sipariş geri yüklendi.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order restore error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Sipariş geri yüklenirken bir hata oluştu.');
        }
    }

    /**
     * Siparişi iptal et - eserleri serbest bırak, ArtPuan iade et
     */
    protected function cancelOrder(Order $order)
    {
        DB::beginTransaction();

        try {
            $previousStatus = $order->status;
            $order->update(['status' => 'cancelled']);

            $messages = [];

            // Eserleri serbest bırak (reserved veya sold)
            foreach ($order->items as $item) {
                if ($item->artwork) {
                    $item->artwork->update([
                        'is_reserved' => false,
                        'is_sold' => false,
                    ]);
                }
            }
            $messages[] = 'Eserler tekrar satışa açıldı.';

            $buyer = $order->user;

            // 1) Kullanıcının harcadığı ArtPuan'ı iade et
            if ($order->artpuan_used > 0 && $buyer) {
                $buyer->addArtPuan((float) $order->artpuan_used, [
                    'type' => 'refund',
                    'order_id' => $order->id,
                    'description' => "#{$order->order_number} sipariş iptali - " . number_format($order->artpuan_used, 2, ',', '.') . " AP iade",
                ]);
                $messages[] = number_format($order->artpuan_used, 2, ',', '.') . ' AP kullanıcıya iade edildi.';
            }

            // 2) Sipariş onaylanmışsa dağıtılan ArtPuan'ları geri al
            if (in_array($previousStatus, ['confirmed', 'shipped', 'delivered']) && $buyer) {
                // Alıcıya verilmiş satın alma ArtPuan'ını geri al
                $buyerPuanLog = \App\Models\ArtPuanLog::where('order_id', $order->id)
                    ->where('user_id', $buyer->id)
                    ->where('type', 'purchase')
                    ->first();

                if ($buyerPuanLog && $buyerPuanLog->amount > 0) {
                    $clawbackAmount = min((float) $buyerPuanLog->amount, (float) $buyer->art_puan);
                    if ($clawbackAmount > 0) {
                        $buyer->spendArtPuan($clawbackAmount, [
                            'order_id' => $order->id,
                            'description' => "#{$order->order_number} sipariş iptali - satın alma ArtPuan geri alındı",
                        ]);
                        $messages[] = number_format($clawbackAmount, 2, ',', '.') . ' AP satın alma puanı geri alındı.';
                    }
                }

                // Referans sahibinden verilmiş ArtPuan'ı geri al
                if ($buyer->referred_by) {
                    $referrer = User::find($buyer->referred_by);
                    if ($referrer) {
                        $referrerPuanLog = \App\Models\ArtPuanLog::where('order_id', $order->id)
                            ->where('user_id', $referrer->id)
                            ->where('type', 'referral')
                            ->first();

                        if ($referrerPuanLog && $referrerPuanLog->amount > 0) {
                            $clawbackAmount = min((float) $referrerPuanLog->amount, (float) $referrer->art_puan);
                            if ($clawbackAmount > 0) {
                                $referrer->spendArtPuan($clawbackAmount, [
                                    'order_id' => $order->id,
                                    'source_user_id' => $buyer->id,
                                    'description' => "#{$order->order_number} sipariş iptali - referans ArtPuan geri alındı",
                                ]);
                                $messages[] = 'Referans sahibinden ' . number_format($clawbackAmount, 2, ',', '.') . ' AP geri alındı.';
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Sipariş iptal edildi. ' . implode(' ', $messages));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Siparis iptal hatasi: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Sipariş iptal edilirken bir hata oluştu.');
        }
    }
}
