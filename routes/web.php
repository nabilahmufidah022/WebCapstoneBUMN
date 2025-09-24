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

Route::get('dashboard',[UserController::class,'goDashboard'])->name('dashboard');
