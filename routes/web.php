<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenawaranPrintController;
use App\Http\Controllers\PurchaseOrderPrintController;
use App\Http\Controllers\SuratJalanPrintController;
use App\Http\Controllers\InvoicePrintController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ReportPrintController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin/penawarans/{penawaran}/print', PenawaranPrintController::class)
    ->middleware('auth')->name('penawarans.print');
Route::get('/admin/purchase-orders/{purchaseOrder}/print', PurchaseOrderPrintController::class)
    ->middleware('auth')->name('purchase-orders.print');
Route::get('/admin/surat-jalans/{suratJalan}/print', SuratJalanPrintController::class)
    ->middleware('auth')->name('surat-jalans.print');
Route::get('/admin/invoices/{invoice}/print', InvoicePrintController::class)
    ->middleware('auth')->name('invoices.print');
Route::get('/admin/reports/export', ReportExportController::class)
    ->middleware('auth')->name('admin.reports.export');
Route::get('/admin/reports/print', ReportPrintController::class)
    ->middleware('auth')->name('admin.reports.print');
