<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportDeclarationController;
use App\Http\Controllers\ImportDeclarationController;
use App\Http\Controllers\ReexportDeclarationController;
use App\Http\Controllers\ReimportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SerialController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Tờ khai tạm nhập
Route::resource('import-declarations', ImportDeclarationController::class);
Route::post('import-declarations/upload-excel', [ImportDeclarationController::class, 'uploadExcel'])
    ->name('import-declarations.upload-excel');
Route::post('import-declarations/{importDeclaration}/extend', [ImportDeclarationController::class, 'extend'])
    ->name('import-declarations.extend');

// Tờ khai tạm xuất
Route::resource('export-declarations', ExportDeclarationController::class);
Route::post('export-declarations/upload-excel', [ExportDeclarationController::class, 'uploadExcel'])
    ->name('export-declarations.upload-excel');
Route::post('export-declarations/{exportDeclaration}/attach-serials', [ExportDeclarationController::class, 'attachSerials'])
    ->name('export-declarations.attach-serials');
Route::delete('export-declarations/{exportDeclaration}/detach-serial/{serialId}', [ExportDeclarationController::class, 'detachSerial'])
    ->name('export-declarations.detach-serial');

// Tái nhập
Route::post('reimport', [ReimportController::class, 'store'])->name('reimport.store');

// Xuất trả nước ngoài
Route::resource('reexport-declarations', ReexportDeclarationController::class);

// Serial number
Route::get('serials/search', [SerialController::class, 'search'])->name('serials.search');
Route::get('serials/{serial}/history', [SerialController::class, 'history'])->name('serials.history');

// Báo cáo
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/rental-revenue', [ReportController::class, 'rentalRevenue'])->name('reports.rental-revenue');