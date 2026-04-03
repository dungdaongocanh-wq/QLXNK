<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng tái nhập (khách trả máy về)
        Schema::create('reimport_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->cascadeOnDelete();
            $table->date('reimport_date')->comment('Ngày tái nhập');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('condition_note')->nullable()->comment('Ghi chú tình trạng hàng khi nhận lại');
            $table->timestamps();
        });

        // Bảng serial trong phiếu tái nhập
        Schema::create('reimport_serial_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimport_id')->constrained('reimport_records')->cascadeOnDelete();
            $table->foreignId('serial_id')->constrained('equipment_serials')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimport_serial_items');
        Schema::dropIfExists('reimport_records');
    }
};
