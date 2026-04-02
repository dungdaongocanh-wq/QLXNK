<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class EquipmentSerial extends Model
{
    protected $fillable = [
        'import_item_id',
        'serial_number',
        'status',
        'current_export_id',
        'notes',
    ];

    /**
     * Mặt hàng tờ khai tạm nhập chứa serial này
     */
    public function importItem(): BelongsTo
    {
        return $this->belongsTo(ImportDeclarationItem::class, 'import_item_id');
    }

    /**
     * Tờ khai tạm xuất hiện tại
     */
    public function currentExport(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class, 'current_export_id');
    }

    /**
     * Lịch sử tờ khai tạm xuất của serial này
     */
    public function exportSerialItems(): HasMany
    {
        return $this->hasMany(ExportSerialItem::class, 'serial_id');
    }

    /**
     * Lịch sử tái nhập của serial này
     */
    public function reimportSerialItems(): HasMany
    {
        return $this->hasMany(ReimportSerialItem::class, 'serial_id');
    }

    /**
     * Scope: lọc serial đang trong kho
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('status', 'in_stock');
    }

    /**
     * Scope: lọc serial đang cho thuê
     */
    public function scopeRentedOut(Builder $query): Builder
    {
        return $query->where('status', 'rented_out');
    }
}
