<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AdminPasalController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
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

        Route::get('users/{user}/change-password', [AdminAccountController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{user}/update-password', [AdminAccountController::class, 'updatePassword'])->name('users.update-password');
    });
});