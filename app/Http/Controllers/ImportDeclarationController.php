<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use App\Models\ImportDeclaration;
use App\Models\ImportDeclarationItem;
use App\Services\ExcelImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImportDeclarationController extends Controller
{
    public function __construct(
        private ExcelImportService $excelImportService
    ) {}

    public function index(): View
    {
        $declarations = ImportDeclaration::with('items')
            ->orderByDesc('registration_date')
            ->paginate(20);

        return view('import-declarations.index', compact('declarations'));
    }

    public function create(): View
    {
        return view('import-declarations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number' => 'required|string|max:20|unique:import_declarations',
            'registration_date'  => 'required|date',
            'expiry_date'        => 'required|date|after:registration_date',
            'importer_name'      => 'required|string|max:200',
        ]);

        $validated['created_by'] = auth()->id();
        ImportDeclaration::create($validated);

        return redirect()->route('import-declarations.index')
            ->with('success', 'Tờ khai tạm nhập đã được tạo thành công.');
    }

    public function show(ImportDeclaration $importDeclaration): View
    {
        $importDeclaration->load([
            'items.serials.currentExport',
            'createdBy',
            'exportDeclarations.customer',
            'extensionHistories',
        ]);

        return view('import-declarations.show', compact('importDeclaration'));
    }

    public function edit(ImportDeclaration $importDeclaration): View
    {
        $importDeclaration->load(['items.serials']);
        return view('import-declarations.edit', compact('importDeclaration'));
    }

    /**
     * Cập nhật toàn bộ tờ khai + items + serials
     */
    public function update(Request $request, ImportDeclaration $importDeclaration): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number'  => 'required|string|max:20|unique:import_declarations,declaration_number,' . $importDeclaration->id,
            'registration_date'   => 'required|date',
            'expiry_date'         => 'required|date',
            'status'              => 'required|in:active,extended,re_exported,expired',
            'importer_name'       => 'nullable|string|max:200',
            'importer_code'       => 'nullable|string|max:50',
            'exporter_name'       => 'nullable|string|max:200',
            'exporter_country'    => 'nullable|string|max:10',
            'customs_office'      => 'nullable|string|max:100',
            'customs_type_code'   => 'nullable|string|max:30',
            'invoice_number'      => 'nullable|string|max:100',
            'invoice_date'        => 'nullable|string|max:50',
            'total_invoice_value' => 'nullable|numeric',
            'currency'            => 'nullable|string|max:5',
            'bill_of_lading'      => 'nullable|string|max:100',
            'package_quantity'    => 'nullable|numeric',
            'package_unit'        => 'nullable|string|max:10',
            'gross_weight'        => 'nullable|numeric',
            'weight_unit'         => 'nullable|string|max:10',
            'notes'               => 'nullable|string',

            // Items
            'items'                        => 'nullable|array',
            'items.*.id'                   => 'nullable|exists:import_declaration_items,id',
            'items.*.hs_code'              => 'nullable|string|max:20',
            'items.*.equipment_name'       => 'nullable|string|max:500',
            'items.*.model'                => 'nullable|string|max:100',
            'items.*.origin_country'       => 'nullable|string|max:10',
            'items.*.quantity'             => 'nullable|integer|min:0',
            'items.*.quantity_unit'        => 'nullable|string|max:10',
            'items.*.unit_price'           => 'nullable|numeric',
            'items.*.total_value'          => 'nullable|numeric',
            'items.*.price_currency'       => 'nullable|string|max:5',

            // Serials cho từng item
            'items.*.serials'              => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $importDeclaration) {
            // Cập nhật header
            $importDeclaration->update(collect($validated)->except('items')->toArray());

            // Cập nhật items
            $submittedItems = $validated['items'] ?? [];
            $submittedIds   = collect($submittedItems)->pluck('id')->filter()->values();

            // Xóa items không còn trong form
            $importDeclaration->items()
                ->whereNotIn('id', $submittedIds)
                ->each(function ($item) {
                    // Trả serial về in_stock trước khi xóa
                    $item->serials()->update(['status' => 'in_stock', 'import_item_id' => null]);
                    $item->delete();
                });

            foreach ($submittedItems as $idx => $itemData) {
                if (!empty($itemData['id'])) {
                    // Cập nhật item có sẵn
                    $item = ImportDeclarationItem::find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'hs_code'        => $itemData['hs_code'] ?? null,
                            'equipment_name' => $itemData['equipment_name'] ?? null,
                            'model'          => $itemData['model'] ?? null,
                            'origin_country' => $itemData['origin_country'] ?? null,
                            'quantity'       => $itemData['quantity'] ?? 1,
                            'quantity_unit'  => $itemData['quantity_unit'] ?? null,
                            'unit_price'     => $itemData['unit_price'] ?? null,
                            'total_value'    => $itemData['total_value'] ?? null,
                            'price_currency' => $itemData['price_currency'] ?? 'USD',
                            'item_sequence'  => $idx + 1,
                        ]);
                        // Cập nhật serials
                        $this->syncSerials($item, $itemData['serials'] ?? '');
                    }
                } else {
                    // Tạo item mới
                    $item = $importDeclaration->items()->create([
                        'hs_code'        => $itemData['hs_code'] ?? null,
                        'equipment_name' => $itemData['equipment_name'] ?? null,
                        'model'          => $itemData['model'] ?? null,
                        'origin_country' => $itemData['origin_country'] ?? null,
                        'quantity'       => $itemData['quantity'] ?? 1,
                        'quantity_unit'  => $itemData['quantity_unit'] ?? null,
                        'unit_price'     => $itemData['unit_price'] ?? null,
                        'total_value'    => $itemData['total_value'] ?? null,
                        'price_currency' => $itemData['price_currency'] ?? 'USD',
                        'item_sequence'  => $idx + 1,
                    ]);
                    $this->syncSerials($item, $itemData['serials'] ?? '');
                }
            }
        });

        return redirect()->route('import-declarations.show', $importDeclaration)
            ->with('success', 'Tờ khai đã được cập nhật.');
    }

    /**
     * Đồng bộ serial cho một item:
     * - Thêm serial mới (in_stock)
     * - Giữ serial cũ vẫn còn trong danh sách
     * - Serial bị xóa khỏi danh sách → trả về in_stock độc lập
     */
    private function syncSerials(ImportDeclarationItem $item, string $serialsRaw): void
    {
        $newNumbers = collect(preg_split('/[\r\n,;\/]+/', $serialsRaw))
            ->map(fn($s) => trim($s))
            ->filter()
            ->unique()
            ->values();

        $existingSerials = $item->serials()->get()->keyBy('serial_number');

        // Thêm serial mới chưa có
        foreach ($newNumbers as $number) {
            if (!$existingSerials->has($number)) {
                // Kiểm tra serial này có đang thuộc item khác không
                $existing = EquipmentSerial::where('serial_number', $number)->first();
                if ($existing) {
                    // Cập nhật về item này
                    $existing->update(['import_item_id' => $item->id, 'status' => $existing->status === 'in_stock' ? 'in_stock' : $existing->status]);
                } else {
                    EquipmentSerial::create([
                        'import_item_id' => $item->id,
                        'serial_number'  => $number,
                        'status'         => 'in_stock',
                    ]);
                }
            }
        }

        // Serial bị xóa khỏi danh sách → tách khỏi item (không xóa, giữ trạng thái)
        foreach ($existingSerials as $number => $serial) {
            if (!$newNumbers->contains($number)) {
                $serial->update(['import_item_id' => null]);
            }
        }
    }

    public function destroy(ImportDeclaration $importDeclaration): RedirectResponse
    {
        $importDeclaration->delete();

        return redirect()->route('import-declarations.index')
            ->with('success', 'Tờ khai đã được xóa.');
    }

    public function uploadExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file     = $request->file('excel_file');
        $path     = $file->store('excel/import-declarations', 'local');
        $fullPath = \Storage::disk('local')->path($path);

        try {
            $parsedData  = $this->excelImportService->parseImportDeclaration($fullPath);
            $declaration = $this->excelImportService->importToDatabase($parsedData, auth()->id() ?? 1);
            $declaration->update(['excel_file_path' => $path]);

            return redirect()->route('import-declarations.show', $declaration)
                ->with('success', 'Import file Excel thành công! Đã tạo tờ khai ' . $declaration->declaration_number);
        } catch (\Exception $e) {
            \Storage::disk('local')->delete($path);
            return redirect()->back()->with('error', 'Lỗi khi import file: ' . $e->getMessage());
        }
    }

    /**
     * Gia hạn tờ khai
     */
    public function extend(Request $request, ImportDeclaration $importDeclaration): RedirectResponse
    {
        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:' . $importDeclaration->expiry_date,
            'extension_doc'   => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $importDeclaration) {
            $importDeclaration->extensionHistories()->create([
                'old_expiry_date' => $importDeclaration->expiry_date,
                'new_expiry_date' => $validated['new_expiry_date'],
                'extension_doc'   => $validated['extension_doc'] ?? null,
                'notes'           => $validated['notes'] ?? null,
                'extended_by'     => auth()->id(),
            ]);

            $importDeclaration->update([
                'expiry_date' => $validated['new_expiry_date'],
                'status'      => 'extended',
            ]);
        });

        return redirect()->route('import-declarations.show', $importDeclaration)
            ->with('success', 'Gia hạn tờ khai thành công.');
    }
}