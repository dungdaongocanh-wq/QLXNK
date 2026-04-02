<?php

namespace App\Http\Controllers;

use App\Models\ExportDeclaration;
use App\Models\ImportDeclaration;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard tổng quan.
     */
    public function index(): View
    {
        $importStats = [
            'total'        => ImportDeclaration::count(),
            'active'       => ImportDeclaration::where('status', 'active')->count(),
            'expiring_30d' => ImportDeclaration::expiringSoon(30)->count(),
            'expiring_7d'  => ImportDeclaration::expiringSoon(7)->count(),
        ];

        $exportStats = [
            'total'        => ExportDeclaration::count(),
            'active'       => ExportDeclaration::where('status', 'active')->count(),
            'expiring_30d' => ExportDeclaration::expiringSoon(30)->count(),
            'overdue'      => ExportDeclaration::overdue()->count(),
        ];

        $importAlerts  = ImportDeclaration::expiringSoon(30)
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        $exportAlerts = ExportDeclaration::expiringSoon(30)
            ->with('customer')
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'importStats',
            'exportStats',
            'importAlerts',
            'exportAlerts'
        ));
    }
}
