<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportDeclarationItem extends Model
{
    protected $fillable = [
        'export_declaration_id',
        'import_item_id',
        'quantity',
        'unit_price',
        'rental_price_per_day',
        'currency',
    ];

    protected $casts = [
        'unit_price'           => 'decimal:2',
        'rental_price_per_day' => 'decimal:2',
    ];

    /**
     * Tờ khai tạm xuất
     */
    public function exportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class);
    }

    /**
     * Mặt hàng từ tờ khai tạm nhập
     */
    public function importItem(): BelongsTo
    {
        return $this->belongsTo(ImportDeclarationItem::class, 'import_item_id');
    }
}
