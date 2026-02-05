<?php

use App\Livewire\HomePage;
use App\Livewire\ArtistList;
use App\Livewire\ArtistDetail;
use App\Livewire\ArtworkList;
use App\Livewire\ArtworkDetail;
use App\Livewire\Cart;
use App\Livewire\Checkout;
use App\Livewire\AddressManager;
use App\Livewire\MyAccount;
use App\Livewire\BlogList;
use App\Livewire\BlogDetail;
use App\Livewire\Favorites;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Http\Controllers\ArtworkSubmissionController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
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

// Search API
Route::get('/api/search', [SearchController::class, 'search'])->name('api.search');

// Static Pages
Route::view('/hakkimizda', 'pages.about')->name('about');
Route::view('/iletisim', 'pages.contact')->name('contact');
Route::view('/artpuan', 'pages.artpuan')->name('artpuan');
Route::view('/banka-hesaplari', 'pages.banka-hesaplari')->name('banka-hesaplari');
Route::view('/eser-kabulu', 'pages.eser-kabulu')->name('eser-kabulu');
Route::post('/eser-kabulu', [ArtworkSubmissionController::class, 'submit'])->name('eser-kabulu.submit');

// Blog
Route::get('/blog', BlogList::class)->name('blog');
Route::get('/blog/{slug}', BlogDetail::class)->name('blog.detail');

// Checkout
Route::get('/odeme', Checkout::class)->middleware('auth')->name('checkout');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/giris', Login::class)->name('login');
    Route::get('/kayit', Register::class)->name('register');
});

Route::post('/cikis', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

Route::get('/hesabim/{tab?}', MyAccount::class)->middleware('auth')->name('profile');
Route::get('/favorilerim', Favorites::class)->middleware('auth')->name('favorites');
Route::get('/adreslerim', AddressManager::class)->middleware('auth')->name('addresses');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('artists', App\Http\Controllers\Admin\ArtistController::class)->except(['show']);
    Route::resource('artworks', App\Http\Controllers\Admin\ArtworkController::class)->except(['show']);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::get('messages/sms', [App\Http\Controllers\Admin\MessageController::class, 'smsForm'])->name('messages.sms');
    Route::post('messages/sms', [App\Http\Controllers\Admin\MessageController::class, 'sendSms'])->name('messages.sms.send');
    Route::get('messages/email', [App\Http\Controllers\Admin\MessageController::class, 'emailForm'])->name('messages.email');
    Route::post('messages/email', [App\Http\Controllers\Admin\MessageController::class, 'sendEmail'])->name('messages.email.send');
    Route::resource('blog-categories', App\Http\Controllers\Admin\BlogCategoryController::class)->except(['show']);
    Route::resource('blog-posts', App\Http\Controllers\Admin\BlogPostController::class)->except(['show']);
    Route::get('art-puan-logs', [App\Http\Controllers\Admin\ArtPuanLogController::class, 'index'])->name('art-puan-logs.index');
    Route::get('notification-logs', [App\Http\Controllers\Admin\NotificationLogController::class, 'index'])->name('notification-logs.index');
});
