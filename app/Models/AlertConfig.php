<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AlertConfig extends Model
{
    protected $fillable = [
        'alert_type',
        'days_before',
        'notify_emails',
        'is_active',
    ];

    protected $casts = [
        'notify_emails' => 'array',
        'is_active'     => 'boolean',
    ];

    /**
     * Scope: lọc cấu hình cảnh báo đang hoạt động
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
