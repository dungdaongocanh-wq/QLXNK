<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportDeclarationItem extends Model
{
    protected $fillable = [
        'import_declaration_id',
        'item_sequence',
        'hs_code',
        'description',
        'equipment_name',
        'model',
        'quantity',
        'quantity_unit',
        'unit_price',
        'price_currency',
        'total_value',
        'origin_country',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    /**
     * Tờ khai tạm nhập chứa mặt hàng này
     */
    public function importDeclaration(): BelongsTo
    {
        return $this->belongsTo(ImportDeclaration::class);
    }

    /**
     * Danh sách serial number của mặt hàng
     */
    public function serials(): HasMany
    {
        return $this->hasMany(EquipmentSerial::class, 'import_item_id');
    }

    /**
     * Chi tiết trong tờ khai tạm xuất
     */
    public function exportDeclarationItems(): HasMany
    {
        return $this->hasMany(ExportDeclarationItem::class, 'import_item_id');
    }

    /**
     * Số serial còn trong kho
     */
    public function inStockCount(): int
    {
        return $this->serials()->where('status', 'in_stock')->count();
    }
}
