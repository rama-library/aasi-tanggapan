<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, HomeController, ProfileController,
    RespondController, ReportController,
    AdminAccountController, AdminDocumentController,
    AdminPasalController, AdminPermissionController,
    AdminRespondController, AdminRoleController
};

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/force-login', [AuthController::class, 'forceLogin'])->name('force-login');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('password.update.self');

    // Tanggapan Berlangsung & Final (View)
    Route::prefix('tanggapan-berlangsung')->name('tanggapan.berlangsung')->group(function () {
        Route::get('/', [RespondController::class, 'indexBerlangsung'])->name('');
        Route::get('/{document:slug}/detail', [RespondController::class, 'show'])->name('.detail');
    });

    Route::prefix('tanggapan-final')->name('tanggapan.final')->group(function () {
        Route::get('/', [RespondController::class, 'indexFinal'])->name('');
        Route::get('/{document:slug}/detail', [RespondController::class, 'showFinal'])->name('.detail');
    });

    // Menu Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Role-based Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:PIC|Reviewer'])->prefix('tanggapan-berlangsung/{document:slug}/pasal/{pasal}')->group(function () {
        Route::get('/create', [RespondController::class, 'create'])->name('respond.create');
        Route::post('/', [RespondController::class, 'store'])->name('respond.store');
        Route::get('/respond/{respond}/edit', [RespondController::class, 'edit'])->name('respond.edit');
        Route::put('/respond/{respond}', [RespondController::class, 'update'])->name('respond.update');
    });

    Route::middleware(['role:Reviewer'])->group(function () {
        // Delete di Berlangsung
        Route::delete('tanggapan-berlangsung/{document:slug}/pasal/{pasal}/respond/{respond}', [RespondController::class, 'destroy'])->name('respond.destroy');

        // Edit di Final
        Route::prefix('tanggapan-final/{document:slug}/pasal/{pasal}/respond/{respond}')->group(function () {
            Route::get('/edit', [RespondController::class, 'edit'])->name('tanggapan.final.edit');
            Route::put('/', [RespondController::class, 'update'])->name('tanggapan.final.update');
            Route::delete('/', [RespondController::class, 'destroy'])->name('tanggapan.final.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->as('admin.')->middleware(['role:Main Admin'])->group(function () {
        Route::resource('permissions', AdminPermissionController::class)->except(['show']);
        Route::resource('roles', AdminRoleController::class)->except(['show']);
        Route::resource('users', AdminAccountController::class)->except(['show']);

        // Custom user password routes
        Route::get('users/{user}/change-password', [AdminAccountController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{user}/update-password', [AdminAccountController::class, 'updatePassword'])->name('users.update-password');

        // Dokumen dan Pasal
        Route::get('documents/checkSlug', [AdminDocumentController::class, 'checkSlug'])->name('documents.checkSlug');
        Route::resource('documents', AdminDocumentController::class);

        Route::prefix('documents/{document:slug}/pasal')->group(function () {
            Route::get('/', [AdminPasalController::class, 'index'])->name('pasal.index');
            Route::get('/create', [AdminPasalController::class, 'create'])->name('pasal.create');
            Route::post('/', [AdminPasalController::class, 'store'])->name('pasal.store');
            Route::get('/{pasal}/edit', [AdminPasalController::class, 'edit'])->name('pasal.edit');
            Route::put('/{pasal}', [AdminPasalController::class, 'update'])->name('pasal.update');
            Route::get('/{pasal}', [AdminPasalController::class, 'show'])->name('pasal.show');
        });

        Route::delete('pasal/{pasal}', [AdminPasalController::class, 'destroy'])->name('pasal.destroy');

        // Statistik respond
        Route::get('responds/today', [AdminRespondController::class, 'today'])->name('responds.today');
    });
});
