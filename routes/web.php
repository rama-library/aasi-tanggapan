<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, HomeController, ProfileController,
    RespondController, ReportController,
    AdminAccountController, AdminDocumentController,
    AdminBatangTubuhController, AdminContentController, AdminDocumentTypeController, AdminPermissionController,
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
    Route::prefix('berikan-tanggapan')->name('berikan.tanggapan')->group(function () {
        Route::get('/', [RespondController::class, 'indexBerlangsung'])->name('');
        Route::get('/{document:slug}/detail', [RespondController::class, 'show'])->name('.detail');
    });

    Route::prefix('tanggapan-selesai')->name('tanggapan.selesai')->group(function () {
        Route::get('/', [RespondController::class, 'indexFinal'])->name('');
        Route::get('/{document:slug}/detail', [RespondController::class, 'showFinal'])->name('.detail');
    });

    // Menu Laporan
    Route::prefix('rekap')->name('laporan.')->group(function () {
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
    Route::middleware(['role:PIC|Reviewer'])->prefix('berikan-tanggapan/{document:slug}/content/{content}')->group(function () {
        Route::get('/create', [RespondController::class, 'create'])->name('respond.create');
        Route::post('/', [RespondController::class, 'store'])->name('respond.store');
        Route::get('/respond/{respond}/edit', [RespondController::class, 'edit'])->name('respond.edit');
        Route::put('/respond/{respond}', [RespondController::class, 'update'])->name('respond.update');
        Route::delete('/respond/{respond}', [RespondController::class, 'destroy'])->name('respond.destroy');
    });

    Route::middleware(['role:Reviewer'])->group(function () {

        // Edit di Final
        Route::prefix('tanggapan-selesai/{document:slug}/content/{content}/respond/{respond}')->group(function () {
            Route::get('/edit', [RespondController::class, 'edit'])->name('tanggapan.selesai.edit');
            Route::put('/', [RespondController::class, 'update'])->name('tanggapan.selesai.update');
            Route::delete('/', [RespondController::class, 'destroy'])->name('tanggapan.selesai.destroy');
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
        // routes/web.php inside admin group
        Route::resource('document-types', AdminDocumentTypeController::class)->except(['show']);

        // Custom user password routes
        Route::get('users/{user}/change-password', [AdminAccountController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{user}/update-password', [AdminAccountController::class, 'updatePassword'])->name('users.update-password');

        // Dokumen dan content
        Route::get('documents/checkSlug', [AdminDocumentController::class, 'checkSlug'])->name('documents.checkSlug');
        Route::resource('documents', AdminDocumentController::class);

        Route::prefix('documents/{document:slug}/content')->group(function () {
            Route::get('/', [AdminContentController::class, 'index'])->name('content.index');
            Route::get('/create', [AdminContentController::class, 'create'])->name('content.create');
            Route::post('/', [AdminContentController::class, 'store'])->name('content.store');
            Route::get('/{content}/edit', [AdminContentController::class, 'edit'])->name('content.edit');
            Route::put('/{content}', [AdminContentController::class, 'update'])->name('content.update');
            Route::get('/{content}', [AdminContentController::class, 'show'])->name('content.show');
        });

        Route::delete('content/{content}', [AdminContentController::class, 'destroy'])->name('content.destroy');

        // Statistik respond
        Route::get('responds/today', [AdminRespondController::class, 'today'])->name('responds.today');
        Route::get('picnorespond', [AdminRespondController::class, 'picNoRespond'])->name('picnorespond');

        });
});
