<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AdminPasalController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\AdminRespondController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RespondController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/force-login', [AuthController::class, 'forceLogin'])->name('force-login');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::put('/change-password', [ProfileController::class, 'updatePassword'])
        ->name('password.update.self')
        ->middleware('auth');
    Route::get('/tanggapan-berlangsung', [RespondController::class, 'indexBerlangsung'])->name('tanggapan.berlangsung');
    Route::get('/tanggapan-berlangsung/{document:slug}/detail', [RespondController::class, 'show'])->name('tanggapan.detail');
    Route::get('/tanggapan-final', [RespondController::class, 'indexFinal'])->name('tanggapan.final');
    Route::get('/tanggapan-final/{document:slug}/detail', [RespondController::class, 'showFinal'])->name('tanggapan.final.detail');
    // Menu Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('laporan.export');

    Route::middleware(['role:PIC'])->group(function () {
        Route::get('/tanggapan-berlangsung/{document:slug}/pasal/{pasal}/create', [RespondController::class, 'create'])->name('respond.create');
        Route::post('/tanggapan-berlangsung/{document:slug}/pasal/{pasal}', [RespondController::class, 'store'])->name('respond.store');
    });

    Route::middleware(['role:Reviewer'])->group(function () {
        Route::get('/tanggapan-berlangsung/{document:slug}/pasal/{pasal}/respond/{respond}/edit', [RespondController::class, 'edit'])->name('respond.edit');
        Route::put('/tanggapan-berlangsung/{document:slug}/pasal/{pasal}/respond/{respond}', [RespondController::class, 'update'])->name('respond.update');
        Route::delete('/tanggapan-berlangsung/{document:slug}/pasal/{pasal}/respond/{respond}', [RespondController::class, 'destroy'])->name('respond.destroy');
        
        Route::get('/tanggapan-final/{document:slug}/pasal/{pasal}/respond/{respond}/edit', [RespondController::class, 'edit'])->name('tanggapan.final.edit');
        Route::put('/tanggapan-final/{document:slug}/pasal/{pasal}/respond/{respond}', [RespondController::class, 'update'])->name('tanggapan.final.update');
        Route::delete('/tanggapan-final/{document:slug}/pasal/{pasal}/respond/{respond}', [RespondController::class, 'destroy'])->name('tanggapan.final.destroy');
    }); 

    Route::middleware(['role:Main Admin'])->prefix('admin')->group(function () {
        Route::resource('permissions', AdminPermissionController::class)->except(['show']);
        Route::resource('roles', AdminRoleController::class)->except(['show']);

        Route::resource('users', AdminAccountController::class)->except(['show']);

        Route::get('documents/checkSlug', [AdminDocumentController::class, 'checkSlug']);
        Route::resource('documents', AdminDocumentController::class);

        Route::get('documents/{document}/pasal', [AdminPasalController::class, 'index'])->name('pasal.index');
        Route::get('documents/{document:slug}/pasal/create', [AdminPasalController::class, 'create'])->name('pasal.create');
        Route::post('documents/{document}/pasal', [AdminPasalController::class, 'store'])->name('pasal.store');
        Route::get('documents/{document:slug}/pasal/{pasal}/edit', [AdminPasalController::class, 'edit'])->name('pasal.edit');
        Route::put('documents/{document:slug}/pasal/{pasal}', [AdminPasalController::class, 'update'])->name('pasal.update');
        Route::delete('pasal/{pasal}', [AdminPasalController::class, 'destroy'])->name('pasal.destroy');
        Route::get('documents/{document:slug}/pasal/{pasal}', [AdminPasalController::class, 'show'])->name('pasal.show');

        
        Route::get('responds/today', [AdminRespondController::class, 'today'])->name('responds.today');
        

        Route::get('users/{user}/change-password', [AdminAccountController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{user}/update-password', [AdminAccountController::class, 'updatePassword'])->name('users.update-password');
    });
});