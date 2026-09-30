<?php
use App\Http\Controllers\Admin\MasterDataController;
use Illuminate\Support\Facades\Route;
Route::get('/',fn()=>view('welcome'));
Route::prefix('admin')->name('admin.')->group(function(){
 Route::get('/',[MasterDataController::class,'index'])->name('dashboard');
});