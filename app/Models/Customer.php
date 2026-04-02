<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'tax_code',
        'address',
        'phone',
        'email',
        'contact_person',
    ];

    /**
     * Các tờ khai tạm xuất của khách hàng
     */
    public function exportDeclarations(): HasMany
    {
        return $this->hasMany(ExportDeclaration::class);
    }
}
