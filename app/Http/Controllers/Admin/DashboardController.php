<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'artists' => Artist::count(),
            'artworks' => Artwork::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'total_sales' => Order::whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total_tl'),
            // Her siparişin satış anındaki kurla kaydedilmiş USD tutarlarının toplamı
            'total_sales_usd' => Order::whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total_usd'),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentArtworks = Artwork::with('artist')
            ->latest()
            ->take(5)
            ->get();

        $range = in_array($request->query('ay'), ['12', '24', 'tumu'], true) ? $request->query('ay') : '12';
        $monthly = $this->monthlySales($range);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentArtworks', 'monthly', 'range'));
    }

    /**
     * Aylık satış: ödemesi alınmış siparişler (ödendi/onaylandı/kargoda/teslim edildi),
     * ödeme tarihine göre (yoksa onay, yoksa sipariş tarihi). Boş aylar 0 ile doldurulur.
     *
     * @return array{labels: string[], revenue: float[], count: int[], total: float, orders: int}
     */
    protected function monthlySales(string $range): array
    {
        $dateExpr = 'COALESCE(paid_at, confirmed_at, created_at)';

        $grouped = Order::query()
            ->whereIn('status', ['paid', 'confirmed', 'shipped', 'delivered'])
            ->selectRaw("DATE_FORMAT({$dateExpr}, '%Y-%m') as ym, COUNT(*) as cnt, SUM(total_tl) as revenue")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');
        $rows = $grouped->map(fn ($r) => (float) $r->revenue);
        $counts = $grouped->map(fn ($r) => (int) $r->cnt);

        $end = now()->startOfMonth();
        $start = match ($range) {
            '24' => $end->copy()->subMonths(23),
            'tumu' => $rows->keys()->min() ? Carbon::createFromFormat('Y-m', $rows->keys()->min())->startOfMonth() : $end->copy(),
            default => $end->copy()->subMonths(11),
        };

        $labels = $revenue = $count = [];
        for ($m = $start->copy(); $m <= $end; $m->addMonth()) {
            $key = $m->format('Y-m');
            $labels[] = $m->translatedFormat('M Y');
            $revenue[] = round($rows[$key] ?? 0, 2);
            $count[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'count' => $count,
            'total' => array_sum($revenue),
            'orders' => array_sum($count),
        ];
    }
}
