<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use App\Models\ExportDeclaration;
use App\Models\ExportSerialItem;
use App\Models\ReimportRecord;
use App\Models\ReimportSerialItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReimportController extends Controller
{
    /**
     * Ghi nhận tái nhập (khách trả máy về).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'export_declaration_id' => 'required|exists:export_declarations,id',
            'reimport_date'         => 'required|date',
            'serials'               => 'required|array|min:1',
            'serials.*'             => 'exists:equipment_serials,id',
            'condition_note'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $reimport = ReimportRecord::create([
                'export_declaration_id' => $validated['export_declaration_id'],
                'reimport_date'         => $validated['reimport_date'],
                'received_by'           => auth()->id(),
                'condition_note'        => $validated['condition_note'] ?? null,
            ]);

            foreach ($validated['serials'] as $serialId) {
                ReimportSerialItem::create([
                    'reimport_id' => $reimport->id,
                    'serial_id'   => $serialId,
                ]);

                ExportSerialItem::where('export_declaration_id', $validated['export_declaration_id'])
                    ->where('serial_id', $serialId)
                    ->whereNull('returned_at')
                    ->update(['returned_at' => $validated['reimport_date']]);

                EquipmentSerial::where('id', $serialId)->update([
                    'status'            => 'in_stock',
                    'current_export_id' => null,
                ]);
            }

            $exportDeclaration = ExportDeclaration::find($validated['export_declaration_id']);
            $totalSerials = $exportDeclaration->serialItems()->count();
            $returnedSerials = $exportDeclaration->serialItems()->whereNotNull('returned_at')->count();

            if ($returnedSerials === $totalSerials) {
                $exportDeclaration->update(['status' => 'fully_returned']);
            } elseif ($returnedSerials > 0) {
                $exportDeclaration->update(['status' => 'partially_returned']);
            }
        });

        return redirect()->route('export-declarations.show', $validated['export_declaration_id'])
            ->with('success', 'Đã ghi nhận tái nhập thành công.');
    }
}
