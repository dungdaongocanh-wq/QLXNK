<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportDeclarationController;
use App\Http\Controllers\ImportDeclarationController;
use App\Http\Controllers\ReexportDeclarationController;
use App\Http\Controllers\ReimportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SerialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Hệ thống Quản lý Xuất Nhập Khẩu (QLXNK)
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Tờ khai tạm nhập
Route::resource('import-declarations', ImportDeclarationController::class);
Route::post('import-declarations/upload-excel', [ImportDeclarationController::class, 'uploadExcel'])
    ->name('import-declarations.upload-excel');

// Tờ khai tạm xuất
Route::resource('export-declarations', ExportDeclarationController::class);

// Tái nhập (khách trả máy về)
Route::post('reimport', [ReimportController::class, 'store'])->name('reimport.store');

// Xuất trả nước ngoài
Route::resource('reexport-declarations', ReexportDeclarationController::class);

// Serial number
Route::get('serials/search', [SerialController::class, 'search'])->name('serials.search');
Route::get('serials/{serial}/history', [SerialController::class, 'history'])->name('serials.history');

// Báo cáo
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/rental-revenue', [ReportController::class, 'rentalRevenue'])->name('reports.rental-revenue');
