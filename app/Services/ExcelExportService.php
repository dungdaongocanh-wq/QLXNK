<?php

namespace App\Services;

use App\Models\ExportDeclaration;
use App\Models\ExportDeclarationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExportService
{
    public function parseExportDeclaration(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File không tồn tại: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $totalSheets = $spreadsheet->getSheetCount();

        $sheet1 = $spreadsheet->getSheet(0);
        $header = $this->parseHeader($sheet1);

        $items = [];
        for ($i = 0; $i < $totalSheets; $i++) {
            $sheet     = $spreadsheet->getSheet($i);
            $startRows = $this->findItemStartRows($sheet);
            foreach ($startRows as $startRow) {
                $item = $this->parseItemSection($sheet, $startRow);
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        $header['total_item_lines'] = count($items);

        return ['header' => $header, 'items' => $items];
    }

    /**
     * Tạo tờ khai + lưu danh sách mặt hàng.
     * KHÔNG gán serial — serial gắn thủ công từ màn hình show.
     */
    public function importToDatabase(array $parsedData, int $userId): ExportDeclaration
    {
        return DB::transaction(function () use ($parsedData, $userId) {
            $headerData               = $parsedData['header'];
            $headerData['created_by'] = $userId;

            $declaration = ExportDeclaration::create($headerData);

            foreach ($parsedData['items'] as $itemData) {
                ExportDeclarationItem::create([
                    'export_declaration_id' => $declaration->id,
                    'hs_code'               => $itemData['hs_code'],
                    'description'           => $itemData['description'],
                    'model'                 => $itemData['model'],
                    'origin_country'        => $itemData['origin_country'],
                    'quantity'              => $itemData['quantity'],
                    'quantity_unit'         => $itemData['quantity_unit'],
                    'unit_price'            => $itemData['unit_price'],
                    'total_value'           => $itemData['total_value'],
                    'currency'              => 'USD',
                ]);
            }

            Log::info("Created export #{$declaration->declaration_number}, items: " . count($parsedData['items']));

            return $declaration->fresh(['items']);
        });
    }

    private function parseHeader(Worksheet $sheet): array
    {
        $packageRaw = $this->getCellValue($sheet, 40, 'H');
        $weightRaw  = $this->getCellValue($sheet, 41, 'H');

        return [
            'declaration_number' => $this->getCellValue($sheet, 4, 'E'),
            'customs_type_code'  => $this->getCellValue($sheet, 6, 'L'),
            'inspection_code'    => $this->getCellValue($sheet, 6, 'F'),
            'customs_office'     => $this->getCellValue($sheet, 7, 'J'),
            'registration_date'  => $this->parseDateCell($sheet, 8, 'F'),
            'expiry_date'        => $this->parseDateCell($sheet, 9, 'G'),
            'exporter_tax_code'  => $this->getCellValue($sheet, 13, 'F'),
            'exporter_name'      => $this->getCellValue($sheet, 14, 'F'),
            'importer_name'      => $this->getCellValue($sheet, 30, 'F'),
            'importer_address'   => $this->getCellValue($sheet, 33, 'F'),
            'importer_country'   => $this->getCellValue($sheet, 35, 'F'),
            'package_quantity'   => $this->parseNumericValue($packageRaw),
            'package_unit'       => $this->parseUnit($packageRaw),
            'gross_weight'       => $this->parseNumericValue($weightRaw),
            'weight_unit'        => $this->parseUnit($weightRaw),
            'marks_and_numbers'  => $this->getCellValue($sheet, 47, 'H'),
            'export_notes'       => $this->getCellValue($sheet, 64, 'F'),
            'invoice_number'     => $this->getCellValue($sheet, 49, 'R'),
            'invoice_date'       => $this->getCellValue($sheet, 51, 'S'),
            'total_value'        => $this->parseNumericCell($sheet, 53, 'U'),
            'currency'           => 'USD',
            'status'             => 'active',
        ];
    }

    private function findItemStartRows(Worksheet $sheet): array
    {
        $startRows  = [];
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $value = $this->getCellValue($sheet, $row, 'C');
            if ($value && preg_match('/^<\d+>$/', trim((string)$value))) {
                $startRows[] = $row;
            }
        }
        return $startRows;
    }

    private function parseItemSection(Worksheet $sheet, int $startRow): array
    {
        // +2 = Mã số hàng hóa (F), +3 = Mô tả hàng hóa (F)
        $hsCode      = $this->getCellValue($sheet, $startRow + 2, 'F');
        $description = $this->getCellValue($sheet, $startRow + 3, 'F');

        if (empty($description) && empty($hsCode)) {
            return [];
        }

        $description = (string)($description ?? '');
        $quantity    = (int)($this->parseNumericCell($sheet, $startRow + 6, 'V') ?? 1);
        $totalValue  = $this->parseNumericCell($sheet, $startRow + 8, 'H');
        $unitPrice   = $this->parseNumericCell($sheet, $startRow + 9, 'U');

        return [
            'hs_code'        => $hsCode,
            'description'    => $description,
            'model'          => $this->parseModel($description),
            'origin_country' => $this->parseOriginFromDescription($description),
            'quantity'       => max(1, $quantity),
            'quantity_unit'  => 'PCE',
            'unit_price'     => $unitPrice,
            'total_value'    => $totalValue,
        ];
    }

    private function parseOriginFromDescription(string $description): ?string
    {
        if (preg_match('/#&\s*([A-Za-z]{2})\s*$/u', $description, $matches)) {
            return strtoupper($matches[1]);
        }
        return null;
    }

    private function parseModel(string $description): string
    {
        if (empty($description)) return '';
        if (preg_match('/model[:\s]+([^\s,;#\(]+)/iu', $description, $matches)) {
            return trim($matches[1]);
        }
        if (preg_match('/\bNRT-([\w\-]+)/i', $description, $matches)) {
            return 'NRT-' . $matches[1];
        }
        return '';
    }

    private function getCellValue(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(strtoupper($col));
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
            $value     = $sheet->getCell($cellCoord)->getValue();
            if ($value === null || $value === '') return null;
            return trim((string)$value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseNumericCell(Worksheet $sheet, int $row, string $col): ?float
    {
        $value = $this->getCellValue($sheet, $row, $col);
        if ($value === null) return null;
        $value = str_replace([',', ' '], '', $value);
        return is_numeric($value) ? (float)$value : null;
    }

    private function parseDateCell(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(strtoupper($col));
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
            $value     = $sheet->getCell($cellCoord)->getValue();
            if ($value === null || $value === '') return null;

            if (is_numeric($value)) {
                $ts = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float)$value);
                return date('Y-m-d H:i:s', $ts);
            }

            $value = trim(trim((string)$value), '-/ ');
            foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d'] as $format) {
                $date = \DateTime::createFromFormat($format, $value);
                if ($date !== false) return $date->format('Y-m-d H:i:s');
            }
            return (string)$value;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseNumericValue(?string $value): ?float
    {
        if (empty($value)) return null;
        if (preg_match('/([\d\.]+)/', trim($value), $matches)) return (float)$matches[1];
        return null;
    }

    private function parseUnit(?string $value): ?string
    {
        if (empty($value)) return null;
        if (preg_match('/[\d\.]+\s+([A-Z]+)/i', trim($value), $matches)) return strtoupper($matches[1]);
        return null;
    }
}