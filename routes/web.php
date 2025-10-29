<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, HomeController, ProfileController,
    RespondController, ReportController,
    AdminAccountController, AdminDocumentController,
    AdminBatangTubuhController, AdminPermissionController,
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

    Route::post('/respond/no-respond/{document}', [RespondController::class, 'noRespond'])
    ->name('respond.noRespond')
    ->middleware('role:PIC');

    /*
    |--------------------------------------------------------------------------
    | Role-based Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:PIC|Reviewer'])->prefix('tanggapan-berlangsung/{document:slug}/batangtubuh/{batangtubuh}')->group(function () {
        Route::get('/create', [RespondController::class, 'create'])->name('respond.create');
        Route::post('/', [RespondController::class, 'store'])->name('respond.store');
        Route::get('/respond/{respond}/edit', [RespondController::class, 'edit'])->name('respond.edit');
        Route::put('/respond/{respond}', [RespondController::class, 'update'])->name('respond.update');
    });

    Route::middleware(['role:Reviewer'])->group(function () {
        // Delete di Berlangsung
        Route::delete('tanggapan-berlangsung/{document:slug}/batangtubuh/{batangtubuh}/respond/{respond}', [RespondController::class, 'destroy'])->name('respond.destroy');

        // Edit di Final
        Route::prefix('tanggapan-final/{document:slug}/batangtubuh/{batangtubuh}/respond/{respond}')->group(function () {
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

        // Dokumen dan batangtubuh
        Route::get('documents/checkSlug', [AdminDocumentController::class, 'checkSlug'])->name('documents.checkSlug');
        Route::resource('documents', AdminDocumentController::class);

        Route::prefix('documents/{document:slug}/batangtubuh')->group(function () {
            Route::get('/', [AdminBatangTubuhController::class, 'index'])->name('batangtubuh.index');
            Route::get('/create', [AdminBatangTubuhController::class, 'create'])->name('batangtubuh.create');
            Route::post('/', [AdminBatangTubuhController::class, 'store'])->name('batangtubuh.store');
            Route::get('/{batangtubuh}/edit', [AdminBatangTubuhController::class, 'edit'])->name('batangtubuh.edit');
            Route::put('/{batangtubuh}', [AdminBatangTubuhController::class, 'update'])->name('batangtubuh.update');
            Route::get('/{batangtubuh}', [AdminBatangTubuhController::class, 'show'])->name('batangtubuh.show');
        });

        Route::delete('batangtubuh/{batangtubuh}', [AdminBatangTubuhController::class, 'destroy'])->name('batangtubuh.destroy');

        // Statistik respond
        Route::get('responds/today', [AdminRespondController::class, 'today'])->name('responds.today');
        Route::get('picnorespond', [AdminRespondController::class, 'picNoRespond'])->name('picnorespond');
    });
});
