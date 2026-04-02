<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng chi tiết mặt hàng trong tờ khai tạm nhập
        Schema::create('import_declaration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_declaration_id')->constrained('import_declarations')->cascadeOnDelete();
            $table->integer('item_sequence')->comment('Số thứ tự mặt hàng: 01, 02...');
            $table->string('hs_code', 20)->nullable()->comment('Mã số hàng hóa HS');
            $table->text('description')->nullable()->comment('Mô tả hàng hóa gốc đầy đủ từ Excel');
            $table->string('equipment_name', 200)->nullable()->comment('Tên thiết bị đã parse');
            $table->string('model', 100)->nullable()->comment('Model thiết bị đã parse');
            $table->integer('quantity')->default(1)->comment('Số lượng');
            $table->string('quantity_unit', 10)->nullable()->comment('Đơn vị số lượng, vd: PCE');
            $table->decimal('unit_price', 15, 2)->nullable()->comment('Đơn giá');
            $table->string('price_currency', 5)->nullable()->comment('Đơn vị tiền tệ đơn giá');
            $table->decimal('total_value', 15, 2)->nullable()->comment('Trị giá hóa đơn');
            $table->string('origin_country', 100)->nullable()->comment('Nước xuất xứ');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_declaration_items');
    }
};
