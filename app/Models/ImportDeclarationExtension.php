<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportDeclarationExtension extends Model
{
    protected $table = 'import_declaration_extensions';

    protected $fillable = [
        'import_declaration_id',
        'old_expiry_date',
        'new_expiry_date',
        'extension_doc',
        'notes',
        'extended_by',
    ];

    protected $casts = [
        'old_expiry_date' => 'date',
        'new_expiry_date' => 'date',
    ];

    public function importDeclaration(): BelongsTo
    {
        return $this->belongsTo(ImportDeclaration::class);
    }

    public function extendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extended_by');
    }
}