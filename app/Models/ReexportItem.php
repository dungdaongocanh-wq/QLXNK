<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReexportItem extends Model
{
    protected $fillable = [
        'reexport_declaration_id',
        'import_item_id',
        'serial_id',
        'quantity',
    ];

    /**
     * Tờ khai xuất trả
     */
    public function reexportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ReexportDeclaration::class);
    }

    /**
     * Mặt hàng từ tờ khai tạm nhập
     */
    public function importItem(): BelongsTo
    {
        return $this->belongsTo(ImportDeclarationItem::class, 'import_item_id');
    }

    /**
     * Serial number (nếu có)
     */
    public function serial(): BelongsTo
    {
        return $this->belongsTo(EquipmentSerial::class, 'serial_id');
    }
}
