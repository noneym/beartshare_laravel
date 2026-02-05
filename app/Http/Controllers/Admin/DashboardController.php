<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'artists' => Artist::count(),
            'artworks' => Artwork::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'total_sales' => Order::where('status', 'delivered')->sum('total_tl'),
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

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentArtworks'));
    }
}
