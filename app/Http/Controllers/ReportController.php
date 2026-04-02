<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use App\Models\ExportDeclaration;
use App\Models\ImportDeclaration;
use App\Models\ImportDeclarationItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Báo cáo tồn kho xuất nhập tồn.
     */
    public function inventory(): View
    {
        $items = ImportDeclarationItem::with([
                'importDeclaration',
                'serials',
            ])
            ->get()
            ->map(function ($item) {
                return [
                    'equipment_name' => $item->equipment_name,
                    'model'          => $item->model,
                    'declaration'    => $item->importDeclaration?->declaration_number,
                    'total_qty'      => $item->quantity,
                    'in_stock'       => $item->serials->where('status', 'in_stock')->count(),
                    'rented_out'     => $item->serials->where('status', 'rented_out')->count(),
                    're_exported'    => $item->serials->where('status', 're_exported')->count(),
                ];
            });

        return view('reports.inventory', compact('items'));
    }

    /**
     * Báo cáo doanh thu cho thuê.
     */
    public function rentalRevenue(Request $request): View
    {
        $from = $request->input('from', now()->startOfYear()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $exports = ExportDeclaration::with(['customer', 'serialItems'])
            ->whereBetween('registration_date', [$from, $to])
            ->whereIn('status', ['fully_returned', 'partially_returned', 'active'])
            ->orderByDesc('registration_date')
            ->get();

        return view('reports.rental-revenue', compact('exports', 'from', 'to'));
    }
}
