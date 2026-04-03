<?php

namespace App\Services;

use App\Models\EquipmentSerial;
use App\Models\ImportDeclaration;
use App\Models\ImportDeclarationItem;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelImportService
{
    public function parseImportDeclaration(string $filePath): array
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
            $sheet = $spreadsheet->getSheet($i);
            $startRows = $this->findItemStartRows($sheet);
            foreach ($startRows as $startRow) {
                $item = $this->parseItemSection($sheet, $startRow);
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        return ['header' => $header, 'items' => $items];
    }

    public function importToDatabase(array $parsedData, int $userId): ImportDeclaration
    {
        return DB::transaction(function () use ($parsedData, $userId) {
            $headerData = $parsedData['header'];
            $headerData['created_by'] = $userId;

            $declaration = ImportDeclaration::create($headerData);

            foreach ($parsedData['items'] as $sequence => $itemData) {
                $serials = $itemData['serials'] ?? [];
                unset($itemData['serials']);

                $itemData['import_declaration_id'] = $declaration->id;
                $itemData['item_sequence'] = $sequence + 1;

                $item = ImportDeclarationItem::create($itemData);

                foreach ($serials as $serialNumber) {
                    $serialNumber = trim($serialNumber);
                    if (empty($serialNumber)) continue;
                    EquipmentSerial::create([
                        'import_item_id' => $item->id,
                        'serial_number'  => $serialNumber,
                        'status'         => 'in_stock',
                    ]);
                }
            }

            return $declaration->fresh(['items.serials']);
        });
    }

    private function parseHeader(Worksheet $sheet): array
    {
        return [
            'declaration_number'  => $this->getCellValue($sheet, 4, 'E'),
            'customs_type_code'   => $this->getCellValue($sheet, 6, 'P'),
            'inspection_code'     => $this->getCellValue($sheet, 6, 'I'),
            'customs_office'      => $this->getCellValue($sheet, 7, 'L'),
            'registration_date'   => $this->parseDateCell($sheet, 8, 'G'),
            'expiry_date'         => $this->parseDateCell($sheet, 8, 'AE'),
            'importer_code'       => $this->getCellValue($sheet, 10, 'H'),
            'importer_name'       => $this->getCellValue($sheet, 11, 'H') ?? '',
            'exporter_name'       => $this->getCellValue($sheet, 23, 'H'),
            'exporter_country'    => $this->getCellValue($sheet, 27, 'H'),
            'bill_of_lading'      => $this->getCellValue($sheet, 31, 'D'),   // D31
            'package_quantity'    => $this->parseQuantityValue($this->getCellValue($sheet, 36, 'K')),
            'package_unit'        => $this->parseQuantityUnit($this->getCellValue($sheet, 36, 'K')),
            'gross_weight'        => $this->parseQuantityValue($this->getCellValue($sheet, 37, 'K')),
            'weight_unit'         => $this->parseQuantityUnit($this->getCellValue($sheet, 37, 'K')),
            'invoice_number'      => $this->getCellValue($sheet, 41, 'J'),
            'invoice_currency'    => $this->parseCurrency($this->getCellValue($sheet, 45, 'J')),
            'invoice_total_value' => $this->parseNumericCell($sheet, 45, 'P'),  // P45
            'customs_detail_value'=> $this->getCellValue($sheet, 64, 'D'),      // D64
            'import_notes'        => $this->getCellValue($sheet, 85, 'G'),      // G85
            'status'              => 'active',
        ];
    }

    private function findItemStartRows(Worksheet $sheet): array
    {
        $startRows = [];
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $value = $this->getCellValue($sheet, $row, 'C');
            if ($value && preg_match('/^<\d+>$/', trim((string)$value))) {
                $startRows[] = $row;
            }
        }
        return $startRows;
    }

    /**
     * Parse một mặt hàng từ startRow.
     * startRow     : <01> ở cột C (row 148)  → <02> row 201 (cách 53 dòng)
     * startRow + 1 : HS code ở cột G
     * startRow + 2 : Mô tả hàng hóa ở cột G
     * origin_country: cột X, offset tính từ startRow
     *   <01> row 148 → origin tại row 160 → offset +12
     *   <02> row 201 → origin tại row 213 → offset +12  ✅ đồng nhất
     */
    private function parseItemSection(Worksheet $sheet, int $startRow): array
    {
        $hsCode      = $this->getCellValue($sheet, $startRow + 1, 'G');
        $description = $this->getCellValue($sheet, $startRow + 2, 'G');

        if (empty($description) && empty($hsCode)) {
            return [];
        }

        $description   = (string)($description ?? '');
        $quantity      = $this->parseNumericCell($sheet, $startRow + 5, 'V');
        $quantityUnit  = $this->getCellValue($sheet, $startRow + 5, 'AE');
        $unitPrice     = $this->parseNumericCell($sheet, $startRow + 7, 'V');
        $totalValue    = $this->parseNumericCell($sheet, $startRow + 7, 'I');
        $originCountry = $this->getCellValue($sheet, $startRow + 12, 'X'); // X160, X213 → offset +12

        return [
            'hs_code'        => $hsCode,
            'description'    => $description,
            'equipment_name' => $this->parseEquipmentName($description),
            'model'          => $this->parseModel($description),
            'quantity'       => (int)($quantity ?? 1),
            'quantity_unit'  => $quantityUnit,
            'unit_price'     => $unitPrice,
            'price_currency' => null,
            'total_value'    => $totalValue,
            'origin_country' => $originCountry,
            'serials'        => $this->parseSerials($description),
        ];
    }

    private function parseSerials(string $description): array
    {
        if (empty($description)) return [];
        if (preg_match('/s[eé]ri?[aáảẫ]?l?[:\s]+([\d\/\w\-]+)/iu', $description, $matches)) {
            $serials = explode('/', $matches[1]);
            return array_filter(array_map('trim', $serials), fn($s) => !empty($s));
        }
        return [];
    }

    private function parseModel(string $description): string
    {
        if (empty($description)) return '';
        if (preg_match('/model[:\s]*([\w\-\.\/]+)/iu', $description, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    private function parseEquipmentName(string $description): string
    {
        if (empty($description)) return '';
        return trim($description);
    }

    private function getCellValue(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex  = $this->columnLetterToIndex($col);
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
        $value = str_replace(',', '', $value);
        return is_numeric($value) ? (float)$value : null;
    }

    private function parseDateCell(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex  = $this->columnLetterToIndex($col);
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
            $value     = $sheet->getCell($cellCoord)->getValue();
            if ($value === null || $value === '') return null;

            if (is_numeric($value)) {
                $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float)$value);
                return date('Y-m-d H:i:s', $timestamp);
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

    private function parseQuantityValue(?string $value): ?float
    {
        if (empty($value)) return null;
        if (preg_match('/^([\d\.]+)/', trim($value), $matches)) return (float)$matches[1];
        return is_numeric($value) ? (float)$value : null;
    }

    private function parseQuantityUnit(?string $value): ?string
    {
        if (empty($value)) return null;
        if (preg_match('/[\d\.]+\s+([A-Z]+)/i', trim($value), $matches)) return strtoupper($matches[1]);
        return null;
    }

    private function parseCurrency(?string $value): ?string
    {
        if (empty($value)) return null;
        if (preg_match('/\b([A-Z]{3})\b/', strtoupper($value), $matches)) return $matches[1];
        return null;
    }

    private function columnLetterToIndex(string $letter): int
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(strtoupper($letter));
    }
}