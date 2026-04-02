<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimportRecord extends Model
{
    protected $fillable = [
        'export_declaration_id',
        'reimport_date',
        'received_by',
        'condition_note',
    ];

    protected $casts = [
        'reimport_date' => 'date',
    ];

    /**
     * Tờ khai tạm xuất liên quan
     */
    public function exportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class);
    }

    /**
     * Người nhận hàng tái nhập
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Danh sách serial tái nhập trong phiếu này
     */
    public function serialItems(): HasMany
    {
        return $this->hasMany(ReimportSerialItem::class, 'reimport_id');
    }
}
