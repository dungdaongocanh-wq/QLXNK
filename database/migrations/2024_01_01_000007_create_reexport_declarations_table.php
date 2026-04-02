<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng tờ khai xuất trả nước ngoài
        Schema::create('reexport_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('declaration_number', 20)->unique()->comment('Số tờ khai xuất trả');
            $table->dateTime('registration_date')->comment('Ngày đăng ký');
            $table->foreignId('import_declaration_id')->constrained('import_declarations')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('excel_file_path', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Bảng chi tiết hàng trong tờ khai xuất trả
        Schema::create('reexport_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reexport_declaration_id')->constrained('reexport_declarations')->cascadeOnDelete();
            $table->foreignId('import_item_id')->constrained('import_declaration_items')->cascadeOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('equipment_serials')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reexport_items');
        Schema::dropIfExists('reexport_declarations');
    }
};
