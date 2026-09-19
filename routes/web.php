<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(
    auth()->check() ? 'admin.dashboard' : 'login'
));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::get('/p/{project:slug}', [ClientProjectController::class, 'show'])->name('projects.show');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/regenerate-password', [ProjectController::class, 'regeneratePassword'])
        ->name('projects.regenerate-password');

    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'preview'])
        ->name('invoices.preview');
    Route::post('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])
        ->name('invoices.generate-pdf');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.download-pdf');
});
