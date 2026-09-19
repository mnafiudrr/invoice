<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\ShareController;
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
Route::get('/p/{project:slug}/password', [ClientProjectController::class, 'showPassword'])->name('projects.password');
Route::post('/p/{project:slug}/password', [ClientProjectController::class, 'checkPassword'])
    ->middleware('throttle:project-password')->name('projects.password.check');

Route::middleware('project.access')->group(function () {
    Route::get('/p/{project:slug}/f/{invoice:invoice_number}', [ClientInvoiceController::class, 'show'])
        ->name('invoices.show');
    Route::get('/p/{project:slug}/f/{invoice:invoice_number}/file/{file}', [ClientInvoiceController::class, 'file'])
        ->name('invoices.file');
});

Route::get('/s/{shareLink:token}', [ShareController::class, 'show'])->name('shares.show');
Route::get('/s/{shareLink:token}/password', [ShareController::class, 'showPassword'])->name('shares.password');
Route::post('/s/{shareLink:token}/password', [ShareController::class, 'checkPassword'])
    ->middleware('throttle:project-password')->name('shares.password.check');

Route::middleware('share.access')->group(function () {
    Route::get('/s/{shareLink:token}/pdf', [ShareController::class, 'pdf'])->name('shares.pdf');
    Route::get('/s/{shareLink:token}/file/{file}', [ShareController::class, 'file'])->name('shares.file');
});

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
    Route::post('invoices/{invoice}/status', [InvoiceController::class, 'changeStatus'])
        ->name('invoices.status');
    Route::post('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])
        ->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/files', [InvoiceController::class, 'storeFile'])
        ->name('invoices.files.store');
    Route::delete('invoices/{invoice}/files/{file}', [InvoiceController::class, 'deleteFile'])
        ->name('invoices.files.destroy');
    Route::post('invoices/{invoice}/share', [InvoiceController::class, 'createShare'])
        ->name('invoices.share');
    Route::delete('invoices/{invoice}/share/{shareLink}', [InvoiceController::class, 'revokeShare'])
        ->name('invoices.share.destroy');
});
