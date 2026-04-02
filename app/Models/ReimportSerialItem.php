<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimportSerialItem extends Model
{
    protected $fillable = [
        'reimport_id',
        'serial_id',
    ];

    /**
     * Phiếu tái nhập
     */
    public function reimportRecord(): BelongsTo
    {
        return $this->belongsTo(ReimportRecord::class, 'reimport_id');
    }

    /**
     * Serial number
     */
    public function serial(): BelongsTo
    {
        return $this->belongsTo(EquipmentSerial::class, 'serial_id');
    }
}
