<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ImportDeclaration extends Model
{
    protected $fillable = [
        'declaration_number',
        'first_declaration_ref',
        'inspection_code',
        'customs_type_code',
        'customs_office',
        'registration_date',
        'expiry_date',
        'importer_code',
        'importer_name',
        'exporter_name',
        'exporter_country',
	'value_detail',
	'customs_notes',
        'bill_of_lading',
        'package_quantity',
        'package_unit',
        'gross_weight',
        'weight_unit',
        'invoice_number',
        'invoice_date',
        'invoice_currency',
        'invoice_total_value',
        'total_invoice_value',
        'currency',
        'status',
        'alert_sent_30d',
        'alert_sent_7d',
        'excel_file_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'registration_date'   => 'datetime',
        'expiry_date'         => 'date',
        'invoice_total_value' => 'decimal:2',
        'total_invoice_value' => 'decimal:2',
        'gross_weight'        => 'decimal:3',
        'alert_sent_30d'      => 'boolean',
        'alert_sent_7d'       => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ImportDeclarationItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exportDeclarations(): HasMany
    {
        return $this->hasMany(ExportDeclaration::class);
    }

    public function reexportDeclarations(): HasMany
    {
        return $this->hasMany(ReexportDeclaration::class);
    }

    /**
     * Lịch sử gia hạn tờ khai
     */
    public function extensionHistories(): HasMany
    {
        return $this->hasMany(ImportDeclarationExtension::class)->orderBy('created_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now())
                     ->whereIn('status', ['active', 'extended']);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiry_date', '<', now())
                     ->whereNotIn('status', ['re_exported', 'expired']);
    }
}