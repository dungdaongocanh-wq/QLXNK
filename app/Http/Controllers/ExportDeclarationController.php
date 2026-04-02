<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EquipmentSerial;
use App\Models\ExportDeclaration;
use App\Models\ExportSerialItem;
use App\Models\ImportDeclaration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExportDeclarationController extends Controller
{
    /**
     * Danh sách tờ khai tạm xuất.
     */
    public function index(): View
    {
        $declarations = ExportDeclaration::with(['customer', 'importDeclaration'])
            ->orderByDesc('registration_date')
            ->paginate(20);

        return view('export-declarations.index', compact('declarations'));
    }

    /**
     * Form tạo mới tờ khai tạm xuất.
     */
    public function create(): View
    {
        $customers   = Customer::orderBy('name')->get();
        $imports     = ImportDeclaration::active()->orderByDesc('registration_date')->get();
        $availableSerials = EquipmentSerial::inStock()->with('importItem')->get();

        return view('export-declarations.create', compact('customers', 'imports', 'availableSerials'));
    }

    /**
     * Lưu tờ khai tạm xuất mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number'    => 'required|string|max:20|unique:export_declarations',
            'registration_date'     => 'required|date',
            'expiry_date'           => 'required|date|after:registration_date',
            'customer_id'           => 'nullable|exists:customers,id',
            'import_declaration_id' => 'nullable|exists:import_declarations,id',
            'serials'               => 'required|array|min:1',
            'serials.*'             => 'exists:equipment_serials,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $serialIds = $validated['serials'];
            unset($validated['serials']);
            $validated['created_by'] = auth()->id();

            $declaration = ExportDeclaration::create($validated);

            foreach ($serialIds as $serialId) {
                ExportSerialItem::create([
                    'export_declaration_id' => $declaration->id,
                    'serial_id'             => $serialId,
                ]);

                EquipmentSerial::where('id', $serialId)->update([
                    'status'            => 'rented_out',
                    'current_export_id' => $declaration->id,
                ]);
            }
        });

        return redirect()->route('export-declarations.index')
            ->with('success', 'Tờ khai tạm xuất đã được tạo.');
    }

    /**
     * Xem chi tiết tờ khai tạm xuất.
     */
    public function show(ExportDeclaration $exportDeclaration): View
    {
        $exportDeclaration->load([
            'customer',
            'importDeclaration',
            'serialItems.serial.importItem',
            'reimportRecords.serialItems.serial',
        ]);

        return view('export-declarations.show', compact('exportDeclaration'));
    }

    /**
     * Form chỉnh sửa tờ khai tạm xuất.
     */
    public function edit(ExportDeclaration $exportDeclaration): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('export-declarations.edit', compact('exportDeclaration', 'customers'));
    }

    /**
     * Cập nhật tờ khai tạm xuất.
     */
    public function update(Request $request, ExportDeclaration $exportDeclaration): RedirectResponse
    {
        $validated = $request->validate([
            'expiry_date' => 'required|date',
            'status'      => 'required|in:active,partially_returned,fully_returned,overdue',
            'notes'       => 'nullable|string',
        ]);

        $exportDeclaration->update($validated);

        return redirect()->route('export-declarations.show', $exportDeclaration)
            ->with('success', 'Tờ khai tạm xuất đã được cập nhật.');
    }

    /**
     * Xóa tờ khai tạm xuất.
     */
    public function destroy(ExportDeclaration $exportDeclaration): RedirectResponse
    {
        $exportDeclaration->delete();

        return redirect()->route('export-declarations.index')
            ->with('success', 'Tờ khai tạm xuất đã được xóa.');
    }
}
