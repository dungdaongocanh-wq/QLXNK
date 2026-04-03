<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_declaration_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_declaration_id')->constrained()->cascadeOnDelete();
            $table->date('old_expiry_date');
            $table->date('new_expiry_date');
            $table->string('extension_document')->nullable()->comment('Số quyết định/văn bản gia hạn');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_declaration_extensions');
    }
};