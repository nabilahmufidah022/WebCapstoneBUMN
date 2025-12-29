<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\MitraParticipationController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('/signup', [UserController::class, 'signup'])->name('register');
Route::post('login', [UserController::class, 'logincheck'])->name('logincheck');
Route::post('signup', [UserController::class, 'registercheck'])->name('registercheck');
Route::middleware('auth')->group(function () {
    Route::get('account',[UserController::class,'account'])->name('account');
    Route::get('account/create',[UserController::class,'createAccount'])->name('account.create');
    Route::post('account',[UserController::class,'storeAccount'])->name('account.store');
    Route::get('account/{id}/edit',[UserController::class,'editAccount'])->name('account.edit');
    Route::put('account/{id}',[UserController::class,'updateAccount'])->name('account.update');
    Route::delete('account/{id}',[UserController::class,'deleteAccount'])->name('account.delete');
});
Route::get('account/{id}/edit',[UserController::class,'editAccount'])->name('account.edit');
Route::put('account/{id}',[UserController::class,'updateAccount'])->name('account.update');
Route::delete('account/{id}',[UserController::class,'deleteAccount'])->name('account.delete');




Route::middleware('auth')->group(function () {
    Route::get('dashboard',[UserController::class,'goDashboard'])->name('dashboard');
    Route::get('daftar_mitra',[MitraController::class,'goDaftarMitra'])->name('daftar_mitra');
    Route::post('daftar_mitra', [MitraController::class, 'store'])->name('store_mitra');
    Route::get('list_mitra', [MitraController::class, 'goListMitra'])->name('list_mitra');
    Route::get('list_mitra/export', [MitraController::class, 'exportListMitra'])->name('list_mitra.export');
    Route::get('kelola_pendaftaran', [MitraController::class, 'goKelolaPendaftaran'])->name('kelola_pendaftaran');
    Route::post('mitra/approve/{id}', [MitraController::class, 'approve'])->name('mitra.approve');
    Route::post('mitra/reject/{id}', [MitraController::class, 'reject'])->name('mitra.reject');
    Route::get('mitra/detail/{id}', [MitraController::class, 'detail'])->name('mitra.detail');

    // Mitra participation feature routes
    Route::get('mitra/participation', [MitraParticipationController::class, 'index'])->name('mitra.participation.index');
    Route::get('mitra/participation/export', [MitraParticipationController::class, 'export'])->name('mitra.participation.export');
    Route::get('mitra/participation/create', [MitraParticipationController::class, 'create'])->name('mitra.participation.create');
    Route::post('mitra/participation', [MitraParticipationController::class, 'store'])->name('mitra.participation.store');
    Route::get('/mitra/participation/{id}', [MitraParticipationController::class, 'show'])->name('mitra.participation.show');
    Route::get('/mitra/participation/{id}/feedback', [MitraParticipationController::class, 'feedback'])->name('mitra.participation.feedback');
    Route::post('/mitra/participation/{id}/feedback', [MitraParticipationController::class, 'storeFeedback'])->name('mitra.participation.feedback.store');
    Route::post('/mitra/participation/feedback/{id}/reply', [MitraParticipationController::class, 'replyFeedback'])->name('mitra.participation.feedback.reply');
    Route::delete('/mitra/participation/{id}', [MitraParticipationController::class, 'destroy'])->name('mitra.participation.destroy');

    Route::get('profile',[UserController::class,'profile'])->name('profile');
    Route::post('profile',[UserController::class,'updateProfile'])->name('updateProfile');
    Route::get('settings',[UserController::class,'settings'])->name('settings');
    Route::post('change-password',[UserController::class,'changePassword'])->name('changePassword');
    Route::post('logout',[UserController::class,'logout'])->name('logout');
});
