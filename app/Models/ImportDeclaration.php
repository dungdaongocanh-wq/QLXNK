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
        'bill_of_lading',
        'package_quantity',
        'package_unit',
        'gross_weight',
        'weight_unit',
        'invoice_number',
        'invoice_currency',
        'invoice_total_value',
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
        'gross_weight'        => 'decimal:3',
        'alert_sent_30d'      => 'boolean',
        'alert_sent_7d'       => 'boolean',
    ];

    /**
     * Chi tiết mặt hàng trong tờ khai
     */
    public function items(): HasMany
    {
        return $this->hasMany(ImportDeclarationItem::class);
    }

    /**
     * Người tạo tờ khai
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Tờ khai tạm xuất liên quan
     */
    public function exportDeclarations(): HasMany
    {
        return $this->hasMany(ExportDeclaration::class);
    }

    /**
     * Tờ khai xuất trả liên quan
     */
    public function reexportDeclarations(): HasMany
    {
        return $this->hasMany(ReexportDeclaration::class);
    }

    /**
     * Scope: lọc tờ khai đang hoạt động
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: lọc tờ khai sắp hết hạn trong N ngày
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now())
                     ->whereIn('status', ['active', 'extended']);
    }

    /**
     * Scope: lọc tờ khai đã hết hạn
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiry_date', '<', now())
                     ->whereNotIn('status', ['re_exported', 'expired']);
    }
}
