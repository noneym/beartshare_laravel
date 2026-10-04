<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SortsIndex;
use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    use SortsIndex;

    public function index(Request $request)
    {
        $query = NotificationLog::with(['user', 'order']);

        // Başlık sıralaması (yoksa en yeni üstte)
        $this->applySort($query, $request, [
            'created' => 'notification_logs.created_at',
            'channel' => 'notification_logs.channel',
            'type' => 'notification_logs.type',
            'recipient' => 'notification_logs.recipient',
            'order' => fn (Builder $q, string $dir) => $q->orderBy(
                Order::select('order_number')->whereColumn('orders.id', 'notification_logs.order_id'), $dir
            ),
            'status' => 'notification_logs.status',
        ]) || $query->latest();

        // Kanal filtresi
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tip filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        // Istatistikler
        $stats = [
            'total' => NotificationLog::count(),
            'sms_count' => NotificationLog::where('channel', 'sms')->count(),
            'email_count' => NotificationLog::where('channel', 'email')->count(),
            'success_count' => NotificationLog::where('status', 'success')->count(),
            'failed_count' => NotificationLog::where('status', 'failed')->count(),
        ];

        return view('admin.notification-logs.index', compact('logs', 'stats'));
    }
}
