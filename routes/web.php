<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:manage-user')->group(function() {
        Route::get('/users',[UserController::class, 'index'])->name('user.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/users',[UserController::class, 'store'])->name('user.store');
        Route::get('/users/edit/{user}', [UserController::class, 'edit']);
        Route::put('/users/{user}',[UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        });

     //Ships
     Route::get('/ship',[ShipController::class, 'index'])->name('ship.index');
     Route::middleware('can:manage-ship')->group(function() {
        Route::get('/ship/create', [ShipController::class, 'create'])->name('ship.create');

        Route::post('/ship',[ShipController::class, 'store']);
        Route::get('/ship/edit/{ship}', [ShipController::class, 'edit']);
        Route::put('/ship/{ship}',[ShipController::class, 'update']);
        Route::delete('/ship/{ship}', [ShipController::class, 'destroy']);
        });

    Route::middleware('can:manage-ticket')->group(function() {
        Route::get('/ticket/index', [TicketController::class,  'index'])->name('ticket.index');
        Route::get('/ticket/create', [TicketController::class,  'create'])->name('ticket.create');
        Route::post('/ticket', [TicketController::class,  'store']);
        Route::post('/ticket/accept/{ticket}', [TicketController::class, 'accept']);
        Route::post('/ticket/cancel/{ticket}', [TicketController::class, 'cancel']);
        Route::get('/ticket/create/{ship}', [TicketController::class, 'withship']);
        Route::post('/tickets', [TicketController::class, 'storeShip']);
    });
});

require __DIR__.'/auth.php';
