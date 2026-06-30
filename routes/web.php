<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\MitraParticipationController;

/*
|--------------------------------------------------------------------------
| Web Routes - Rumah BUMN Jakarta
|--------------------------------------------------------------------------
*/

// =======================================================
// Guest Routes (Public)
// =======================================================

Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('/signup', [UserController::class, 'signup'])->name('register');

Route::post('login', [UserController::class, 'logincheck'])->name('logincheck');
Route::post('signup', [UserController::class, 'registercheck'])->name('registercheck');

// =======================================================
// Forgot Password
// =======================================================

Route::get('/forgot-password', function () {
    return view('login/forgot-password');
})->name('password.request');

Route::post('/forgot-password', [UserController::class, 'checkEmailForReset'])
    ->name('password.email');

Route::get('/reset-password', function () {

    if (!session()->has('reset_email')) {
        return redirect()->route('login');
    }

    return view('login/reset-password');

})->name('password.reset.page');

Route::post('/reset-password', [UserController::class, 'executeInAppReset'])
    ->name('password.update.execute');

// =======================================================
// Public Mitra Registration
// =======================================================

Route::get('daftar_mitra', [MitraController::class, 'goDaftarMitra'])
    ->name('daftar_mitra');

Route::post('daftar_mitra', [MitraController::class, 'store'])
    ->name('store_mitra');

// =======================================================
// Authenticated Routes
// =======================================================

Route::middleware('auth')->group(function () {

    // ==========================
    // Dashboard & Profile
    // ==========================

    Route::get('dashboard', [UserController::class, 'goDashboard'])
        ->name('dashboard');

    Route::get('profile', [UserController::class, 'profile'])
        ->name('profile');

    Route::post('profile', [UserController::class, 'updateProfile'])
        ->name('updateProfile');

    Route::post('profile/handover', [UserController::class, 'handoverPIC'])
        ->name('profile.handover');

    Route::delete('profile', [UserController::class, 'destroySelf'])
        ->name('profile.destroy');

    Route::get('settings', [UserController::class, 'settings'])
        ->name('settings');

    Route::post('change-password', [UserController::class, 'changePassword'])
        ->name('changePassword');

    Route::post('logout', [UserController::class, 'logout'])
        ->name('logout');

    // ==========================
    // Account Management
    // ==========================

    Route::get('account', [UserController::class, 'account'])
        ->name('account');

    Route::get('account/create', [UserController::class, 'createAccount'])
        ->name('account.create');

    Route::post('account', [UserController::class, 'storeAccount'])
        ->name('account.store');

    Route::get('account/{id}/edit', [UserController::class, 'editAccount'])
        ->name('account.edit');

    Route::put('account/{id}', [UserController::class, 'updateAccount'])
        ->name('account.update');

    Route::delete('account/{id}', [UserController::class, 'deleteAccount'])
        ->name('account.delete');

    Route::delete('account/{id}/destroy', [UserController::class, 'destroyPermanently'])
        ->name('account.destroy');

    // ==========================
    // Partnership & Mitra
    // ==========================

    Route::get('list_mitra', [MitraController::class, 'goListMitra'])
        ->name('list_mitra');

    Route::get('list_mitra/export', [MitraController::class, 'exportListMitra'])
        ->name('list_mitra.export');

    Route::get('kelola_pendaftaran', [MitraController::class, 'goKelolaPendaftaran'])
        ->name('kelola_pendaftaran');

    Route::get('mitra/detail/{id}', [MitraController::class, 'detail'])
        ->name('mitra.detail');

    // ==========================
    // Verifikasi Mitra
    // ==========================

    // GET
    Route::get('mitra/review/{id}', [MitraController::class, 'review'])
        ->name('mitra.participation.review');

    Route::get('mitra/approve/{id}', [MitraController::class, 'approve'])
        ->name('mitra.participation.approve');

    Route::get('mitra/reject/{id}', [MitraController::class, 'reject'])
        ->name('mitra.participation.reject');

    // POST
    Route::post('mitra/review/{id}', [MitraController::class, 'review'])
        ->name('mitra.review');

    Route::post('mitra/approve/{id}', [MitraController::class, 'approve'])
        ->name('mitra.approve');

    Route::post('mitra/reject/{id}', [MitraController::class, 'reject'])
        ->name('mitra.reject');

    // Tambahan
    Route::post('mitra/store-manual', [MitraController::class, 'storeManual'])
        ->name('mitra.storeManual');

    Route::get('mitra/detail-identitas/{id}', [MitraController::class, 'detailIdentitas'])
        ->name('mitra.detailIdentitas');

    // ==========================
    // Silabus & Participation
    // ==========================

    Route::prefix('mitra/participation')->group(function () {

        // Dashboard
        Route::get('/', [MitraParticipationController::class, 'index'])
            ->name('mitra.participation.index');

        Route::post('/', [MitraParticipationController::class, 'store'])
            ->name('mitra.participation.store');

        Route::get('/export', [MitraParticipationController::class, 'export'])
            ->name('mitra.participation.export');

        Route::get('/create', [MitraParticipationController::class, 'create'])
            ->name('mitra.participation.create');

        // Edit
        Route::get('/{id}/edit', [MitraParticipationController::class, 'edit'])
            ->name('mitra.participation.edit');

        Route::put('/{id}', [MitraParticipationController::class, 'update'])
            ->name('mitra.participation.update');

        // Complete & Rating
        Route::get('/{id}/complete', [MitraParticipationController::class, 'complete'])
            ->name('mitra.participation.complete');

        Route::post('/{id}/rate', [MitraParticipationController::class, 'rate'])
            ->name('mitra.participation.rate');

        // Delete
        Route::delete('/{id}/destroy', [MitraParticipationController::class, 'destroy'])
            ->name('mitra.participation.destroy');

        // Feedback
        Route::get('/{id}/feedback', [MitraParticipationController::class, 'feedback'])
            ->name('mitra.participation.feedback');

        Route::post('/{id}/feedback/store', [MitraParticipationController::class, 'storeFeedback'])
            ->name('mitra.participation.feedback.store');

        Route::post('/feedback/{id}/reply', [MitraParticipationController::class, 'replyFeedback'])
            ->name('mitra.participation.feedback.reply');

        // Detail (HARUS PALING BAWAH)
        Route::get('/{id}', [MitraParticipationController::class, 'show'])
            ->name('mitra.participation.show');
    });
});
