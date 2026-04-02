<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SerialController extends Controller
{
    /**
     * Tìm kiếm serial number.
     */
    public function search(Request $request): JsonResponse|View
    {
        $query = $request->input('q', '');

        $serials = EquipmentSerial::where('serial_number', 'like', "%{$query}%")
            ->with(['importItem.importDeclaration', 'currentExport.customer'])
            ->limit(20)
            ->get();

        if ($request->wantsJson()) {
            return response()->json($serials);
        }

        return view('serials.search', compact('serials', 'query'));
    }

    /**
     * Lịch sử di chuyển của một serial.
     */
    public function history(EquipmentSerial $serial): View
    {
        $serial->load([
            'importItem.importDeclaration',
            'exportSerialItems.exportDeclaration.customer',
            'reimportSerialItems.reimportRecord',
        ]);

        return view('serials.history', compact('serial'));
    }
}
