<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportSerialItem extends Model
{
    protected $fillable = [
        'export_declaration_id',
        'serial_id',
        'returned_at',
        'condition_on_return',
    ];

    protected $casts = [
        'returned_at' => 'date',
    ];

    /**
     * Tờ khai tạm xuất
     */
    public function exportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class);
    }

    /**
     * Serial number
     */
    public function serial(): BelongsTo
    {
        return $this->belongsTo(EquipmentSerial::class, 'serial_id');
    }

    /**
     * Kiểm tra đã tái nhập về chưa
     */
    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }
}
