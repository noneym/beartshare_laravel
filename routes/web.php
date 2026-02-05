<?php

use App\Livewire\HomePage;
use App\Livewire\ArtistList;
use App\Livewire\ArtistDetail;
use App\Livewire\ArtworkList;
use App\Livewire\ArtworkDetail;
use App\Livewire\Cart;
use App\Livewire\BlogList;
use App\Livewire\BlogDetail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', HomePage::class)->name('home');

// Artists
Route::get('/sanatcilar', ArtistList::class)->name('artists');
Route::get('/sanatci/{slug}', ArtistDetail::class)->name('artist.detail');

// Artworks
Route::get('/eserler', ArtworkList::class)->name('artworks');
Route::get('/eser/{slug}', ArtworkDetail::class)->name('artwork.detail');

// Cart
Route::get('/sepet', Cart::class)->name('cart');

// Static Pages
Route::view('/hakkimizda', 'pages.about')->name('about');
Route::view('/iletisim', 'pages.contact')->name('contact');
Route::view('/artpuan', 'pages.artpuan')->name('artpuan');

// Blog
Route::get('/blog', BlogList::class)->name('blog');
Route::get('/blog/{slug}', BlogDetail::class)->name('blog.detail');

// Checkout
Route::view('/odeme', 'pages.checkout')->name('checkout');

// Auth Routes (placeholder)
Route::view('/giris', 'auth.login')->name('login');
Route::view('/kayit', 'auth.register')->name('register');
Route::view('/profil', 'pages.profile')->name('profile');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('artists', App\Http\Controllers\Admin\ArtistController::class)->except(['show']);
    Route::resource('artworks', App\Http\Controllers\Admin\ArtworkController::class)->except(['show']);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
});
