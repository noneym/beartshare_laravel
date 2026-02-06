<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtPuanLog;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArtPuanLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ArtPuanLog::with(['user', 'order', 'artwork', 'sourceUser'])
            ->latest();

        // Tip filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Kullanıcı araması
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Silinmişleri göster
        if ($request->has('with_trashed')) {
            $query->withTrashed();
        }

        $logs = $query->paginate(30)->withQueryString();

        // Özet istatistikler
        $stats = [
            'total_distributed' => ArtPuanLog::where('amount', '>', 0)->sum('amount'),
            'purchase_total' => ArtPuanLog::where('type', 'purchase')->sum('amount'),
            'referral_total' => ArtPuanLog::where('type', 'referral')->sum('amount'),
            'log_count' => ArtPuanLog::count(),
        ];

        return view('admin.art-puan-logs.index', compact('logs', 'stats'));
    }

    /**
     * Manuel ArtPuan ekleme formu
     */
    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'art_puan']);

        return view('admin.art-puan-logs.create', compact('users'));
    }

    /**
     * Manuel ArtPuan ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:bonus,manual,refund',
            'description' => 'required|string|max:500',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);

            // ArtPuan ekle
            $user->addArtPuan((float) $validated['amount'], [
                'type' => $validated['type'],
                'description' => $validated['description'] . ' (Admin tarafından eklendi)',
            ]);

            Log::info('Manual ArtPuan added', [
                'user_id' => $user->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'added_by' => auth()->user()->name ?? 'Admin',
            ]);

            DB::commit();

            // Bildirim gönder
            $notificationChannels = [];
            if ($request->boolean('notify_sms')) {
                $notificationChannels[] = 'sms';
            }
            if ($request->boolean('notify_email')) {
                $notificationChannels[] = 'email';
            }

            if (!empty($notificationChannels)) {
                try {
                    $notificationService = new NotificationService();
                    $notificationService->notifyManualArtPuan(
                        $user,
                        (float) $validated['amount'],
                        $validated['description'],
                        $notificationChannels
                    );
                } catch (\Exception $e) {
                    Log::error('ArtPuan bildirim hatasi: ' . $e->getMessage());
                }
            }

            $notifyMessage = '';
            if (!empty($notificationChannels)) {
                $notifyMessage = ' Bildirim gönderildi: ' . implode(', ', array_map(fn($c) => $c === 'sms' ? 'SMS' : 'E-posta', $notificationChannels));
            }

            return redirect()->route('admin.art-puan-logs.index')
                ->with('success', "{$user->name} kullanıcısına {$validated['amount']} AP eklendi.{$notifyMessage}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual ArtPuan add error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'ArtPuan eklenirken bir hata oluştu.')
                ->withInput();
        }
    }

    /**
     * ArtPuan log kaydını sil (soft delete) ve kullanıcıdan puanı geri al
     */
    public function destroy(ArtPuanLog $artPuanLog)
    {
        DB::beginTransaction();

        try {
            $user = $artPuanLog->user;
            $amount = (float) $artPuanLog->amount;

            // Eğer pozitif bir puan eklenmişse, kullanıcıdan geri al
            if ($amount > 0 && $user) {
                // Kullanıcının mevcut puanından fazla geri alma yapma
                $clawbackAmount = min($amount, (float) $user->art_puan);

                if ($clawbackAmount > 0) {
                    $user->decrement('art_puan', $clawbackAmount);

                    // Geri alma log kaydı
                    ArtPuanLog::create([
                        'user_id' => $user->id,
                        'type' => 'spend',
                        'amount' => -$clawbackAmount,
                        'balance_after' => $user->art_puan,
                        'description' => "Log #{$artPuanLog->id} silindi - {$clawbackAmount} AP geri alındı (Admin)",
                    ]);
                }
            }

            // Log kaydını soft delete yap
            $artPuanLog->delete();

            Log::info('ArtPuan log deleted', [
                'log_id' => $artPuanLog->id,
                'user_id' => $user->id ?? null,
                'amount' => $amount,
                'deleted_by' => auth()->user()->name ?? 'Admin',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'ArtPuan kaydı silindi ve puan geri alındı.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ArtPuan log delete error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Silme işlemi sırasında bir hata oluştu.');
        }
    }

    /**
     * Silinen kaydı geri yükle
     */
    public function restore($id)
    {
        $log = ArtPuanLog::withTrashed()->findOrFail($id);
        $log->restore();

        return redirect()->back()
            ->with('success', 'ArtPuan kaydı geri yüklendi.');
    }
}
