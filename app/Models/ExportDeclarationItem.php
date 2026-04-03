<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportDeclarationItem extends Model
{
    protected $fillable = [
        'export_declaration_id',
        'hs_code',
        'description',
        'model',
        'origin_country',
        'quantity',
        'quantity_unit',
        'unit_price',
        'total_value',
        'currency',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    public function exportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class);
    }
}