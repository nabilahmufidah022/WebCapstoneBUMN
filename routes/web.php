<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('/signup', [UserController::class, 'signup'])->name('register');
Route::post('login', [UserController::class, 'logincheck'])->name('logincheck');
Route::post('signup', [UserController::class, 'registercheck'])->name('registercheck');

Route::middleware('auth')->group(function () {
    Route::get('dashboard',[UserController::class,'goDashboard'])->name('dashboard');
    Route::get('daftar_mitra',[UserController::class,'goDaftarMitra'])->name('daftar_mitra');
    Route::get('profile',[UserController::class,'profile'])->name('profile');
    Route::post('profile',[UserController::class,'updateProfile'])->name('updateProfile');
    Route::get('settings',[UserController::class,'settings'])->name('settings');
    Route::post('change-password',[UserController::class,'changePassword'])->name('changePassword');
    Route::post('logout',[UserController::class,'logout'])->name('logout');
});
