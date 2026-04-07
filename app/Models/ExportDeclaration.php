<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ExportDeclaration extends Model
{
    protected $fillable = [
        'declaration_number',
        'customs_type_code',
        'inspection_code',
        'customs_office',
        'registration_date',
        'expiry_date',
        'exporter_name',
        'exporter_tax_code',
        'importer_name',
        'importer_address',
        'importer_country',
        'customer_id',
        'import_declaration_id',  // nullable, không bắt buộc
        'package_quantity',
        'package_unit',
        'gross_weight',
        'weight_unit',
        'marks_and_numbers',
        'export_notes',
        'invoice_number',
        'invoice_date',
        'total_value',
        'total_item_lines',
        'currency',
        'status',
        'alert_sent_30d',
        'alert_sent_7d',
        'excel_file_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'expiry_date'       => 'date',
        'total_value'       => 'decimal:2',
        'gross_weight'      => 'decimal:3',
        'alert_sent_30d'    => 'boolean',
        'alert_sent_7d'     => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Không bắt buộc — chỉ dùng khi muốn tham chiếu
    public function importDeclaration(): BelongsTo
    {
        return $this->belongsTo(ImportDeclaration::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExportDeclarationItem::class);
    }

    public function serialItems(): HasMany
    {
        return $this->hasMany(ExportSerialItem::class);
    }

    public function reimportRecords(): HasMany
    {
        return $this->hasMany(ReimportRecord::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now())
                     ->where('status', 'active');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('expiry_date', '<', now())
                     ->where('status', 'active');
    }
}