<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa bảng export_declaration_items cũ (có FK bắt buộc import_item_id)
        // rồi tạo lại không có FK đó
        Schema::dropIfExists('export_declaration_items');

        Schema::create('export_declaration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->cascadeOnDelete();
            $table->string('hs_code', 20)->nullable()->comment('Mã HS từ tờ khai xuất');
            $table->text('description')->nullable()->comment('Mô tả hàng hóa');
            $table->string('model', 100)->nullable();
            $table->string('origin_country', 10)->nullable();
            $table->integer('quantity')->default(1);
            $table->string('quantity_unit', 10)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->string('currency', 5)->default('USD');
            $table->timestamps();
        });

        // Bỏ FK import_declaration_id (không bắt buộc liên kết)
        // Giữ cột nhưng nullable (đã nullable sẵn)
    }

    public function down(): void
    {
        Schema::dropIfExists('export_declaration_items');

        Schema::create('export_declaration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->cascadeOnDelete();
            $table->foreignId('import_item_id')->constrained('import_declaration_items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('rental_price_per_day', 15, 2)->nullable();
            $table->string('currency', 5)->default('USD');
            $table->timestamps();
        });
    }
};