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
        'customs_office',
        'registration_date',
        'expiry_date',
        'customer_id',
        'import_declaration_id',
        'invoice_number',
        'total_value',
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
        'alert_sent_30d'    => 'boolean',
        'alert_sent_7d'     => 'boolean',
    ];

    /**
     * Khách hàng thuê
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Tờ khai tạm nhập gốc
     */
    public function importDeclaration(): BelongsTo
    {
        return $this->belongsTo(ImportDeclaration::class);
    }

    /**
     * Người tạo tờ khai
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Chi tiết mặt hàng trong tờ khai tạm xuất
     */
    public function items(): HasMany
    {
        return $this->hasMany(ExportDeclarationItem::class);
    }

    /**
     * Danh sách serial theo tờ khai tạm xuất
     */
    public function serialItems(): HasMany
    {
        return $this->hasMany(ExportSerialItem::class);
    }

    /**
     * Phiếu tái nhập của tờ khai này
     */
    public function reimportRecords(): HasMany
    {
        return $this->hasMany(ReimportRecord::class);
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
                     ->where('status', 'active');
    }

    /**
     * Scope: lọc tờ khai đã quá hạn
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('expiry_date', '<', now())
                     ->where('status', 'active');
    }
}
