<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order.user'])
            ->latest();

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('auth_code', 'like', "%{$search}%")
                    ->orWhere('host_ref_num', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        // Silinmişleri göster
        if ($request->has('with_trashed')) {
            $query->withTrashed();
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(PaymentTransaction $payment)
    {
        $payment->load(['order.user', 'order.items']);

        return view('admin.payments.show', compact('payment'));
    }

    public function create()
    {
        $orders = Order::whereIn('status', ['pending', 'paid'])
            ->latest()
            ->get();

        return view('admin.payments.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:255',
            'auth_code' => 'nullable|string|max:50',
            'host_ref_num' => 'nullable|string|max:100',
            'card_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $payment = PaymentTransaction::create([
            'order_id' => $validated['order_id'],
            'gateway' => $validated['gateway'],
            'amount' => $validated['amount'],
            'currency' => 'TRY',
            'status' => $validated['status'],
            'transaction_id' => $validated['transaction_id'] ?? 'MANUAL-' . time(),
            'auth_code' => $validated['auth_code'],
            'host_ref_num' => $validated['host_ref_num'],
            'card_number' => $validated['card_number'],
            'request_data' => [
                'manual_entry' => true,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->user()->name ?? 'Admin',
            ],
        ]);

        // Ödeme başarılı ise siparişi güncelle
        if ($validated['status'] === 'completed') {
            $order = Order::find($validated['order_id']);
            if ($order && $order->status === 'pending') {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Ödeme kaydı başarıyla oluşturuldu.');
    }

    public function destroy(PaymentTransaction $payment)
    {
        $orderNumber = $payment->order->order_number ?? 'N/A';
        $payment->delete();

        Log::info('Payment soft deleted', [
            'payment_id' => $payment->id,
            'order_number' => $orderNumber,
            'deleted_by' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->back()
            ->with('success', 'Ödeme kaydı silindi.');
    }

    public function restore($id)
    {
        $payment = PaymentTransaction::withTrashed()->findOrFail($id);
        $payment->restore();

        return redirect()->back()
            ->with('success', 'Ödeme kaydı geri yüklendi.');
    }

    public function forceDelete($id)
    {
        $payment = PaymentTransaction::withTrashed()->findOrFail($id);
        $payment->forceDelete();

        return redirect()->back()
            ->with('success', 'Ödeme kaydı kalıcı olarak silindi.');
    }
}
