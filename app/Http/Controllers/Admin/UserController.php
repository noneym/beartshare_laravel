<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtPuanLog;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['orders', 'favorites', 'referrals'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tc_no', 'like', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhere('referral_code', $search);
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                match ($request->input('role')) {
                    'admin' => $query->where('is_admin', true),
                    'user' => $query->where('is_admin', false),
                    default => $query,
                };
            })
            ->when($request->filled('has_orders'), function ($query) use ($request) {
                match ($request->input('has_orders')) {
                    'yes' => $query->has('orders'),
                    'no' => $query->doesntHave('orders'),
                    default => $query,
                };
            })
            ->when($request->filled('has_artpuan'), function ($query) use ($request) {
                match ($request->input('has_artpuan')) {
                    'yes' => $query->where('art_puan', '>', 0),
                    'no' => $query->where('art_puan', '<=', 0),
                    default => $query,
                };
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->input('sort')) {
                    'oldest' => $query->oldest(),
                    'name' => $query->orderBy('name', 'asc'),
                    'artpuan_desc' => $query->orderBy('art_puan', 'desc'),
                    'artpuan_asc' => $query->orderBy('art_puan', 'asc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['orders', 'favorites', 'referrals']);

        $orders = $user->orders()
            ->latest()
            ->take(10)
            ->get();

        $artPuanLogs = $user->artPuanLogs()
            ->latest()
            ->take(15)
            ->get();

        $notificationLogs = NotificationLog::where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get();

        $referrer = $user->referrer;
        $referrals = $user->referrals()->latest()->take(10)->get();

        $totalSpent = $user->orders()->whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total_tl');

        return view('admin.users.show', compact('user', 'orders', 'artPuanLogs', 'notificationLogs', 'referrer', 'referrals', 'totalSpent'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'tc_no' => 'nullable|string|max:11|unique:users,tc_no,' . $user->id,
            'is_admin' => 'boolean',
            'art_puan' => 'nullable|numeric|min:0',
        ]);

        // ArtPuan manual adjustment
        if (isset($validated['art_puan']) && (float) $validated['art_puan'] !== (float) $user->art_puan) {
            $diff = (float) $validated['art_puan'] - (float) $user->art_puan;
            if ($diff > 0) {
                $user->addArtPuan($diff, [
                    'type' => 'manual',
                    'description' => 'Admin tarafindan manuel eklendi',
                ]);
            } elseif ($diff < 0) {
                $user->art_puan = $validated['art_puan'];
                $user->save();
                ArtPuanLog::create([
                    'user_id' => $user->id,
                    'type' => 'manual',
                    'amount' => $diff,
                    'balance_after' => $validated['art_puan'],
                    'description' => 'Admin tarafindan manuel duzenlendi',
                ]);
            }
            unset($validated['art_puan']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Kullanici bilgileri guncellendi.');
    }
}
