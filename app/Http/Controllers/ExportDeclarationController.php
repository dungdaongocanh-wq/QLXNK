<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EquipmentSerial;
use App\Models\ExportDeclaration;
use App\Models\ExportSerialItem;
use App\Services\ExcelExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExportDeclarationController extends Controller
{
    public function __construct(
        private ExcelExportService $excelExportService
    ) {}

    public function index(): View
    {
        $declarations = ExportDeclaration::with(['customer'])
            ->orderByDesc('registration_date')
            ->paginate(20);

        return view('export-declarations.index', compact('declarations'));
    }

    public function create(): View
    {
        $customers        = Customer::orderBy('name')->get();
        $availableSerials = EquipmentSerial::where('status', 'in_stock')
            ->with('importItem')
            ->orderBy('serial_number')
            ->get();

        return view('export-declarations.create', compact('customers', 'availableSerials'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number' => 'required|string|max:20|unique:export_declarations',
            'registration_date'  => 'required|date',
            'expiry_date'        => 'required|date|after:registration_date',
            'customer_id'        => 'nullable|exists:customers,id',
            'serials'            => 'nullable|array',
            'serials.*'          => 'exists:equipment_serials,id',
        ]);

        DB::transaction(function () use ($validated) {
            $serialIds = $validated['serials'] ?? [];
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

    public function show(ExportDeclaration $exportDeclaration): View
    {
        $exportDeclaration->load([
            'customer',
            'items',
            'serialItems.serial.importItem',
            'reimportRecords.serialItems.serial',
        ]);

        // Tất cả serial đang trong kho — không bắt buộc từ tờ khai nhập nào
        $availableSerials = EquipmentSerial::where('status', 'in_stock')
            ->with('importItem')
            ->orderBy('serial_number')
            ->get();

        return view('export-declarations.show', compact('exportDeclaration', 'availableSerials'));
    }

    public function edit(ExportDeclaration $exportDeclaration): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('export-declarations.edit', compact('exportDeclaration', 'customers'));
    }

    public function update(Request $request, ExportDeclaration $exportDeclaration): RedirectResponse
{
    $validated = $request->validate([
        'declaration_number' => 'required|string|max:20|unique:export_declarations,declaration_number,' . $exportDeclaration->id,
        'customs_type_code'  => 'nullable|string|max:30',
        'inspection_code'    => 'nullable|string|max:10',
        'customs_office'     => 'nullable|string|max:100',
        'registration_date'  => 'required|date',
        'expiry_date'        => 'required|date',
        'exporter_name'      => 'nullable|string|max:200',
        'exporter_tax_code'  => 'nullable|string|max:50',
        'importer_name'      => 'nullable|string|max:200',
        'importer_address'   => 'nullable|string|max:500',
        'importer_country'   => 'nullable|string|max:10',
        'customer_id'        => 'nullable|exists:customers,id',
        'package_quantity'   => 'nullable|numeric',
        'package_unit'       => 'nullable|string|max:10',
        'gross_weight'       => 'nullable|numeric',
        'weight_unit'        => 'nullable|string|max:10',
        'marks_and_numbers'  => 'nullable|string',
        'invoice_number'     => 'nullable|string|max:100',
        'invoice_date'       => 'nullable|string|max:50',
        'total_value'        => 'nullable|numeric',
        'currency'           => 'nullable|string|max:5',
        'export_notes'       => 'nullable|string',
        'status'             => 'required|in:active,partially_returned,fully_returned,overdue',
        'notes'              => 'nullable|string',
    ]);

    $exportDeclaration->update($validated);

    return redirect()->route('export-declarations.show', $exportDeclaration)
        ->with('success', 'Đã cập nhật tờ khai.');
}

    public function destroy(ExportDeclaration $exportDeclaration): RedirectResponse
    {
        $exportDeclaration->delete();

        return redirect()->route('export-declarations.index')
            ->with('success', 'Đã xóa tờ khai tạm xuất.');
    }

    public function uploadExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file     = $request->file('excel_file');
        $path     = $file->store('excel/export-declarations', 'local');
        $fullPath = \Storage::disk('local')->path($path);

        try {
            $parsedData  = $this->excelExportService->parseExportDeclaration($fullPath);
            $declaration = $this->excelExportService->importToDatabase($parsedData, auth()->id() ?? 1);
            $declaration->update(['excel_file_path' => $path]);

            return redirect()->route('export-declarations.show', $declaration)
                ->with('success', 'Import thành công tờ khai ' . $declaration->declaration_number . '. Vui lòng gắn serial bên dưới.');
        } catch (\Exception $e) {
            \Storage::disk('local')->delete($path);
            return redirect()->back()->with('error', 'Lỗi khi import: ' . $e->getMessage());
        }
    }

    /**
     * Gắn serial vào tờ khai — lấy từ bất kỳ serial in_stock nào trong kho
     */
    public function attachSerials(Request $request, ExportDeclaration $exportDeclaration): RedirectResponse
    {
        $request->validate([
            'serial_ids'   => 'required|array|min:1',
            'serial_ids.*' => 'exists:equipment_serials,id',
        ]);

        $attached = 0;

        DB::transaction(function () use ($request, $exportDeclaration, &$attached) {
            foreach ($request->serial_ids as $serialId) {
                $exists = ExportSerialItem::where('export_declaration_id', $exportDeclaration->id)
                    ->where('serial_id', $serialId)->exists();

                if (!$exists) {
                    ExportSerialItem::create([
                        'export_declaration_id' => $exportDeclaration->id,
                        'serial_id'             => $serialId,
                    ]);
                    EquipmentSerial::where('id', $serialId)->update([
                        'status'            => 'rented_out',
                        'current_export_id' => $exportDeclaration->id,
                    ]);
                    $attached++;
                }
            }
        });

        return redirect()->route('export-declarations.show', $exportDeclaration)
            ->with('success', "Đã gắn $attached serial thành công.");
    }

    /**
     * Gỡ serial — trả về in_stock
     */
    public function detachSerial(ExportDeclaration $exportDeclaration, int $serialId): RedirectResponse
    {
        DB::transaction(function () use ($exportDeclaration, $serialId) {
            ExportSerialItem::where('export_declaration_id', $exportDeclaration->id)
                ->where('serial_id', $serialId)->delete();

            EquipmentSerial::where('id', $serialId)
                ->where('current_export_id', $exportDeclaration->id)
                ->update(['status' => 'in_stock', 'current_export_id' => null]);
        });

        return redirect()->route('export-declarations.show', $exportDeclaration)
            ->with('success', 'Đã gỡ serial, serial trả về trạng thái Trong kho.');
    }
}