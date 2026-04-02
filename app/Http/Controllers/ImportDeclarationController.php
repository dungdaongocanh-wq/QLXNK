<?php

namespace App\Http\Controllers;

use App\Models\ImportDeclaration;
use App\Services\ExcelImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportDeclarationController extends Controller
{
    public function __construct(
        private ExcelImportService $excelImportService
    ) {}

    /**
     * Danh sách tờ khai tạm nhập.
     */
    public function index(): View
    {
        $declarations = ImportDeclaration::with('items')
            ->orderByDesc('registration_date')
            ->paginate(20);

        return view('import-declarations.index', compact('declarations'));
    }

    /**
     * Form tạo mới tờ khai.
     */
    public function create(): View
    {
        return view('import-declarations.create');
    }

    /**
     * Lưu tờ khai mới (nhập tay).
     */
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

    /**
     * Xem chi tiết tờ khai.
     */
    public function show(ImportDeclaration $importDeclaration): View
    {
        $importDeclaration->load(['items.serials', 'createdBy', 'exportDeclarations.customer']);
        return view('import-declarations.show', compact('importDeclaration'));
    }

    /**
     * Form chỉnh sửa tờ khai.
     */
    public function edit(ImportDeclaration $importDeclaration): View
    {
        return view('import-declarations.edit', compact('importDeclaration'));
    }

    /**
     * Cập nhật tờ khai.
     */
    public function update(Request $request, ImportDeclaration $importDeclaration): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_number' => 'required|string|max:20|unique:import_declarations,declaration_number,' . $importDeclaration->id,
            'registration_date'  => 'required|date',
            'expiry_date'        => 'required|date',
            'importer_name'      => 'required|string|max:200',
            'status'             => 'required|in:active,extended,re_exported,expired',
        ]);

        $importDeclaration->update($validated);

        return redirect()->route('import-declarations.show', $importDeclaration)
            ->with('success', 'Tờ khai đã được cập nhật.');
    }

    /**
     * Xóa tờ khai.
     */
    public function destroy(ImportDeclaration $importDeclaration): RedirectResponse
    {
        $importDeclaration->delete();

        return redirect()->route('import-declarations.index')
            ->with('success', 'Tờ khai đã được xóa.');
    }

    /**
     * Upload và import file Excel tờ khai tạm nhập.
     */
    public function uploadExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $path = $file->store('excel/import-declarations', 'local');
        $fullPath = storage_path('app/' . $path);

        try {
            $parsedData = $this->excelImportService->parseImportDeclaration($fullPath);
            $declaration = $this->excelImportService->importToDatabase($parsedData, auth()->id() ?? 1);

            $declaration->update(['excel_file_path' => $path]);

            return redirect()->route('import-declarations.show', $declaration)
                ->with('success', 'Import file Excel thành công! Đã tạo tờ khai ' . $declaration->declaration_number);
        } catch (\Exception $e) {
            \Storage::disk('local')->delete($path);

            return redirect()->back()
                ->with('error', 'Lỗi khi import file: ' . $e->getMessage());
        }
    }
}
