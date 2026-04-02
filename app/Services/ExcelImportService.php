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
    /**
     * Parse file Excel tờ khai hải quan tạm nhập.
     * Trả về array ['header' => [...], 'items' => [...]]
     */
    public function parseImportDeclaration(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File không tồn tại: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);

        // Trang 1: Header tờ khai
        $sheet1 = $spreadsheet->getSheet(0);
        $header = $this->parseHeader($sheet1);

        // Trang 3 & 4: Chi tiết mặt hàng (index 2 và 3)
        $items = [];
        $totalSheets = $spreadsheet->getSheetCount();

        for ($i = 2; $i < $totalSheets; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $startRows = $this->findItemStartRows($sheet);

            foreach ($startRows as $startRow) {
                $item = $this->parseItemSection($sheet, $startRow);
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        }

        return [
            'header' => $header,
            'items'  => $items,
        ];
    }

    /**
     * Import dữ liệu đã parse vào database trong transaction.
     */
    public function importToDatabase(array $parsedData, int $userId): ImportDeclaration
    {
        return DB::transaction(function () use ($parsedData, $userId) {
            $headerData = $parsedData['header'];
            $headerData['created_by'] = $userId;

            // Tạo tờ khai tạm nhập
            $declaration = ImportDeclaration::create($headerData);

            // Tạo từng mặt hàng và serial
            foreach ($parsedData['items'] as $sequence => $itemData) {
                $serials = $itemData['serials'] ?? [];
                unset($itemData['serials']);

                $itemData['import_declaration_id'] = $declaration->id;
                $itemData['item_sequence'] = $sequence + 1;

                $item = ImportDeclarationItem::create($itemData);

                // Tạo serial numbers
                foreach ($serials as $serialNumber) {
                    $serialNumber = trim($serialNumber);
                    if (empty($serialNumber)) {
                        continue;
                    }
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

    /**
     * Parse header tờ khai từ Sheet 1.
     * Mapping dựa trên file Excel hải quan chuẩn Việt Nam.
     */
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
            'bill_of_lading'      => $this->getCellValue($sheet, 31, 'D'),
            'package_quantity'    => $this->parseQuantityValue($this->getCellValue($sheet, 36, 'K')),
            'package_unit'        => $this->parseQuantityUnit($this->getCellValue($sheet, 36, 'K')),
            'gross_weight'        => $this->parseQuantityValue($this->getCellValue($sheet, 37, 'K')),
            'weight_unit'         => $this->parseQuantityUnit($this->getCellValue($sheet, 37, 'K')),
            'invoice_number'      => $this->getCellValue($sheet, 41, 'J'),
            'invoice_currency'    => $this->getCellValue($sheet, 45, 'J'),
            'invoice_total_value' => $this->parseNumericCell($sheet, 45, 'P'),
            'status'              => 'active',
        ];
    }

    /**
     * Tìm tất cả row bắt đầu của từng mặt hàng (<01>, <02>...) trong sheet.
     */
    private function findItemStartRows(Worksheet $sheet): array
    {
        $startRows = [];
        $highestRow = $sheet->getHighestRow();

        for ($row = 1; $row <= $highestRow; $row++) {
            foreach (['B', 'C'] as $col) {
                $value = $this->getCellValue($sheet, $row, $col);
                if ($value && preg_match('/^<\d+>$/', trim((string)$value))) {
                    $startRows[] = $row;
                    break;
                }
            }
        }

        return $startRows;
    }

    /**
     * Parse một mặt hàng từ startRow trong sheet.
     */
    private function parseItemSection(Worksheet $sheet, int $startRow): array
    {
        $hsCode = $this->getCellValue($sheet, $startRow + 1, 'G');
        $description = $this->getCellValue($sheet, $startRow + 2, 'G');

        if (empty($description) && empty($hsCode)) {
            return [];
        }

        $description = (string)($description ?? '');
        $quantity = $this->parseNumericCell($sheet, $startRow + 5, 'V');
        $quantityUnit = $this->getCellValue($sheet, $startRow + 5, 'AE');
        $unitPrice = $this->parseNumericCell($sheet, $startRow + 7, 'V');
        $totalValue = $this->parseNumericCell($sheet, $startRow + 7, 'I');
        $originCountry = $this->getCellValue($sheet, $startRow + 12, 'X');

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

    /**
     * Parse serial numbers từ mô tả hàng hóa.
     */
    private function parseSerials(string $description): array
    {
        if (empty($description)) {
            return [];
        }

        if (preg_match('/s[eé]ri?[aáảẫ]?l?[:\s]+([\d\/\w\-]+)/iu', $description, $matches)) {
            $serialString = $matches[1];
            $serials = explode('/', $serialString);
            return array_filter(array_map('trim', $serials), fn($s) => !empty($s));
        }

        return [];
    }

    /**
     * Parse model thiết bị từ mô tả hàng hóa.
     */
    private function parseModel(string $description): string
    {
        if (empty($description)) {
            return '';
        }

        if (preg_match('/model[:\s]*([\w\-\.\/]+)/iu', $description, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    /**
     * Parse tên thiết bị từ mô tả hàng hóa.
     */
    private function parseEquipmentName(string $description): string
    {
        if (empty($description)) {
            return '';
        }

        if (preg_match('/^(.+?),?\s*(?:model|seri)[:\s]/iu', $description, $matches)) {
            return trim(rtrim($matches[1], ', '));
        }

        $parts = explode(',', $description);
        return trim($parts[0]);
    }

    /**
     * Lấy giá trị của ô trong sheet theo row và cột chữ.
     */
    private function getCellValue(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex = $this->columnLetterToIndex($col);
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;

            $cell = $sheet->getCell($cellCoord);
            $value = $cell->getValue();

            if ($value === null || $value === '') {
                return null;
            }

            return trim((string)$value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Lấy giá trị numeric của ô.
     */
    private function parseNumericCell(Worksheet $sheet, int $row, string $col): ?float
    {
        $value = $this->getCellValue($sheet, $row, $col);
        if ($value === null) {
            return null;
        }
        $value = str_replace(',', '', $value);
        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Parse ô ngày tháng từ Excel.
     */
    private function parseDateCell(Worksheet $sheet, int $row, string $col): ?string
    {
        try {
            $colIndex = $this->columnLetterToIndex($col);
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
            $cell = $sheet->getCell($cellCoord);

            $value = $cell->getValue();
            if ($value === null || $value === '') {
                return null;
            }

            if (is_numeric($value)) {
                $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float)$value);
                return date('Y-m-d H:i:s', $timestamp);
            }

            $formats = ['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, trim((string)$value));
                if ($date !== false) {
                    return $date->format('Y-m-d H:i:s');
                }
            }

            return (string)$value;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse giá trị số lượng từ chuỗi như "1 PK", "21 KGM".
     */
    private function parseQuantityValue(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }
        if (preg_match('/^([\d\.]+)/', trim($value), $matches)) {
            return (float)$matches[1];
        }
        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Parse đơn vị từ chuỗi như "1 PK" → "PK".
     */
    private function parseQuantityUnit(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (preg_match('/[\d\.]+\s+([A-Z]+)/i', trim($value), $matches)) {
            return strtoupper($matches[1]);
        }
        return null;
    }

    /**
     * Convert chữ cột Excel thành index.
     */
    private function columnLetterToIndex(string $letter): int
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(strtoupper($letter));
    }
}
