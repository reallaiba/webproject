<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/projects', [MarketplaceController::class, 'projects'])->name('projects.index');
Route::get('/projects/{project}', [MarketplaceController::class, 'showProject'])->name('projects.show');
Route::get('/gigs', [MarketplaceController::class, 'gigs'])->name('gigs.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
    Route::get('/gigs', [AdminController::class, 'gigs'])->name('gigs');
    Route::post('/gigs/{gig}/toggle', [AdminController::class, 'toggleGig'])->name('gigs.toggle');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::get('/alerts', [AdminController::class, 'alerts'])->name('alerts');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [MarketplaceController::class, 'dashboard'])->name('dashboard');

    Route::get('/projects-create', [MarketplaceController::class, 'createProject'])->name('projects.create');
    Route::post('/projects', [MarketplaceController::class, 'storeProject'])->name('projects.store');
    Route::get('/client/projects', [MarketplaceController::class, 'clientProjects'])->name('client.projects');
    Route::get('/projects/{project}/bids', [MarketplaceController::class, 'projectBids'])->name('projects.bids');
    Route::get('/projects/{project}/bid', [MarketplaceController::class, 'bidForm'])->name('bids.create');
    Route::post('/projects/{project}/bid', [MarketplaceController::class, 'storeBid'])->name('bids.store');
    Route::get('/my-bids', [MarketplaceController::class, 'myBids'])->name('my.bids');
    Route::post('/bids/{bid}/hire', [MarketplaceController::class, 'hire'])->name('bids.hire');
    Route::get('/projects/{project}/complete', [MarketplaceController::class, 'completeForm'])->name('projects.complete');
    Route::post('/projects/{project}/complete', [MarketplaceController::class, 'complete'])->name('projects.complete.store');

    Route::get('/gigs-create', [MarketplaceController::class, 'createGig'])->name('gigs.create');
    Route::post('/gigs', [MarketplaceController::class, 'storeGig'])->name('gigs.store');
    Route::get('/my-gigs', [MarketplaceController::class, 'myGigs'])->name('my.gigs');
    Route::post('/gigs/{gig}/cart', [MarketplaceController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [MarketplaceController::class, 'cart'])->name('cart');
    Route::delete('/cart/{item}', [MarketplaceController::class, 'removeCart'])->name('cart.remove');
    Route::get('/checkout', [MarketplaceController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [MarketplaceController::class, 'pay'])->name('checkout.pay');
    Route::get('/orders', [MarketplaceController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [MarketplaceController::class, 'order'])->name('orders.show');

    Route::get('/messages', [MarketplaceController::class, 'messages'])->name('messages');
    Route::post('/messages', [MarketplaceController::class, 'sendMessage'])->name('messages.store');
    Route::get('/notifications', [MarketplaceController::class, 'notifications'])->name('notifications');
    Route::get('/profile', [MarketplaceController::class, 'profile'])->name('profile');
    Route::post('/profile', [MarketplaceController::class, 'updateProfile'])->name('profile.update');
    Route::get('/earnings', [MarketplaceController::class, 'earnings'])->name('earnings');

});
