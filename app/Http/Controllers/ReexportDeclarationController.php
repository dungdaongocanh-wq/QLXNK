<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use App\Models\ImportDeclaration;
use App\Models\ReexportDeclaration;
use App\Models\ReexportItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReexportDeclarationController extends Controller
{
    /**
     * Danh sách tờ khai xuất trả.
     */
    public function index(): View
    {
        $declarations = ReexportDeclaration::with('importDeclaration')
            ->orderByDesc('registration_date')
            ->paginate(20);

        return view('reexport-declarations.index', compact('declarations'));
    }

    /**
     * Form tạo mới tờ khai xuất trả.
     */
    public function create(): View
    {
        $imports = ImportDeclaration::active()->orderByDesc('registration_date')->get();
        return view('reexport-declarations.create', compact('imports'));
    }

    /**
     * Lưu tờ khai xuất trả mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number'    => 'required|string|max:20|unique:reexport_declarations',
            'registration_date'     => 'required|date',
            'import_declaration_id' => 'required|exists:import_declarations,id',
            'items'                 => 'required|array|min:1',
            'items.*.import_item_id' => 'required|exists:import_declaration_items,id',
            'items.*.serial_id'     => 'nullable|exists:equipment_serials,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'notes'                 => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $declaration = ReexportDeclaration::create([
                'declaration_number'    => $validated['declaration_number'],
                'registration_date'     => $validated['registration_date'],
                'import_declaration_id' => $validated['import_declaration_id'],
                'notes'                 => $validated['notes'] ?? null,
                'created_by'            => auth()->id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                ReexportItem::create([
                    'reexport_declaration_id' => $declaration->id,
                    'import_item_id'          => $itemData['import_item_id'],
                    'serial_id'               => $itemData['serial_id'] ?? null,
                    'quantity'                => $itemData['quantity'],
                ]);

                if (!empty($itemData['serial_id'])) {
                    EquipmentSerial::where('id', $itemData['serial_id'])->update([
                        'status' => 're_exported',
                    ]);
                }
            }
        });

        return redirect()->route('reexport-declarations.index')
            ->with('success', 'Tờ khai xuất trả đã được tạo.');
    }

    /**
     * Xem chi tiết tờ khai xuất trả.
     */
    public function show(ReexportDeclaration $reexportDeclaration): View
    {
        $reexportDeclaration->load(['importDeclaration', 'items.importItem', 'items.serial', 'createdBy']);
        return view('reexport-declarations.show', compact('reexportDeclaration'));
    }

    /**
     * Form chỉnh sửa tờ khai xuất trả.
     */
    public function edit(ReexportDeclaration $reexportDeclaration): View
    {
        return view('reexport-declarations.edit', compact('reexportDeclaration'));
    }

    /**
     * Cập nhật tờ khai xuất trả.
     */
    public function update(Request $request, ReexportDeclaration $reexportDeclaration): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $reexportDeclaration->update($validated);

        return redirect()->route('reexport-declarations.show', $reexportDeclaration)
            ->with('success', 'Tờ khai xuất trả đã được cập nhật.');
    }

    /**
     * Xóa tờ khai xuất trả.
     */
    public function destroy(ReexportDeclaration $reexportDeclaration): RedirectResponse
    {
        $reexportDeclaration->delete();

        return redirect()->route('reexport-declarations.index')
            ->with('success', 'Tờ khai xuất trả đã được xóa.');
    }
}
