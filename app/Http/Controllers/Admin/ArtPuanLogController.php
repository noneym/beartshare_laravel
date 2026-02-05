<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtPuanLog;
use Illuminate\Http\Request;

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
}
