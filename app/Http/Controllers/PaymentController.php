<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GarantiPaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected GarantiPaymentService $paymentService;

    public function __construct(GarantiPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Ödeme başlatma formu (hidden form ile redirect)
     */
    public function initiate(Request $request)
    {
        // GET request için session'dan order_id al
        $orderId = $request->input('order_id') ?? session('pending_payment_order_id');

        if (!$orderId) {
            return redirect()->route('checkout')->with('error', 'Sipariş bulunamadı.');
        }

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('checkout')->with('error', 'Sipariş bulunamadı.');
        }

        // Güvenlik: Sadece kendi siparişini ödeyebilsin
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bu siparişe erişim yetkiniz yok.');
        }

        // Sipariş durumu kontrolü
        if ($order->status !== 'pending') {
            return redirect()->route('profile', 'orders')
                ->with('error', 'Bu sipariş için ödeme yapılamaz.');
        }

        $installment = $request->input('installment', 0);

        // Ödeme formunu hazırla
        $paymentData = $this->paymentService->preparePaymentForm($order, $installment);

        return view('payment.redirect', [
            'gatewayUrl' => $paymentData['gateway_url'],
            'formData' => $paymentData['form_data'],
        ]);
    }

    /**
     * Banka callback - Hem success hem error için
     */
    public function callback(Request $request)
    {
        Log::info('Payment Callback', ['request' => $request->all()]);

        // POST verilerini al
        $postData = $request->post();

        if (empty($postData)) {
            Log::error('Payment Callback: No POST data received');
            return redirect()->route('home')
                ->with('error', 'Ödeme işlemi sırasında bir hata oluştu.');
        }

        // Callback'i işle
        $result = $this->paymentService->processCallback($postData);

        if ($result['success']) {
            // Başarılı ödeme - Bildirim gönder
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyPaymentReceived($result['order']);
            } catch (\Exception $e) {
                Log::error('Payment notification error: ' . $e->getMessage());
            }

            return redirect()->route('payment.success', ['order' => $result['order']->id]);
        } else {
            // Başarısız ödeme
            $orderId = $result['order']?->id;

            if ($orderId) {
                return redirect()->route('payment.failed', ['order' => $orderId])
                    ->with('error', $result['message']);
            }

            return redirect()->route('home')
                ->with('error', $result['message']);
        }
    }

    /**
     * Başarılı ödeme sayfası
     */
    public function success(Order $order)
    {
        // Güvenlik kontrolü
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Kazanılan ArtPuan
        $earnedPoints = (int) floor($order->total_tl / 100);

        return view('payment.success', [
            'order' => $order->load('items.artwork'),
            'earnedPoints' => $earnedPoints,
        ]);
    }

    /**
     * Başarısız ödeme sayfası
     */
    public function failed(Order $order)
    {
        // Güvenlik kontrolü
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payment.failed', [
            'order' => $order,
            'error' => session('error', 'Ödeme işlemi başarısız oldu.'),
        ]);
    }
}
