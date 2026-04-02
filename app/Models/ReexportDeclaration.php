<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReexportDeclaration extends Model
{
    protected $fillable = [
        'declaration_number',
        'registration_date',
        'import_declaration_id',
        'notes',
        'excel_file_path',
        'created_by',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

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
     * Chi tiết hàng xuất trả
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReexportItem::class);
    }
}
