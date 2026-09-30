<?php

use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ShipController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/trips', [BookingController::class, 'search'])->name('booking.search');
Route::get('/booking/{trip}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking/{trip}', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success/{order}', [BookingController::class, 'success'])->name('booking.success');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [MasterDataController::class, 'index'])->name('dashboard');
    Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
    Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');
    Route::delete('/routes/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');
    Route::get('/ships', [ShipController::class, 'index'])->name('ships.index');
    Route::post('/ships', [ShipController::class, 'store'])->name('ships.store');
    Route::delete('/ships/{ship}', [ShipController::class, 'destroy'])->name('ships.destroy');
    Route::get('/ticket-types', [TicketTypeController::class, 'index'])->name('ticket-types.index');
    Route::post('/ticket-types', [TicketTypeController::class, 'store'])->name('ticket-types.store');
    Route::delete('/ticket-types/{ticketType}', [TicketTypeController::class, 'destroy'])->name('ticket-types.destroy');
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');
});
