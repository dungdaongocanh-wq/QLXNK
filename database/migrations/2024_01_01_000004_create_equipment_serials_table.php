<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng serial number từng thiết bị
        Schema::create('equipment_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_item_id')->constrained('import_declaration_items')->cascadeOnDelete();
            $table->string('serial_number', 100)->unique();
            $table->enum('status', ['in_stock', 'rented_out', 're_exported', 'lost'])->default('in_stock');
            $table->unsignedBigInteger('current_export_id')->nullable()->comment('Đang thuộc tờ khai tạm xuất nào');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_serials');
    }
};
