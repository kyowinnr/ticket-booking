<?php
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ShipController;
use App\Http\Controllers\Admin\TicketTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn()=>view('welcome'));
Route::prefix('admin')->name('admin.')->group(function(){
 Route::get('/',[MasterDataController::class,'index'])->name('dashboard');
 Route::get('/routes',[RouteController::class,'index'])->name('routes.index');
 Route::post('/routes',[RouteController::class,'store'])->name('routes.store');
 Route::delete('/routes/{route}',[RouteController::class,'destroy'])->name('routes.destroy');
 Route::get('/ships',[ShipController::class,'index'])->name('ships.index');
 Route::post('/ships',[ShipController::class,'store'])->name('ships.store');
 Route::delete('/ships/{ship}',[ShipController::class,'destroy'])->name('ships.destroy');
 Route::get('/ticket-types',[TicketTypeController::class,'index'])->name('ticket-types.index');
 Route::post('/ticket-types',[TicketTypeController::class,'store'])->name('ticket-types.store');
 Route::delete('/ticket-types/{ticketType}',[TicketTypeController::class,'destroy'])->name('ticket-types.destroy');
});