<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng tờ khai tạm xuất (cho thuê)
        Schema::create('export_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('declaration_number', 20)->unique()->comment('Số tờ khai tạm xuất');
            $table->string('customs_type_code', 30)->nullable()->comment('Mã loại hình');
            $table->string('customs_office', 50)->nullable()->comment('Tên CQ Hải quan tiếp nhận');
            $table->dateTime('registration_date')->comment('Ngày đăng ký');
            $table->date('expiry_date')->comment('Hạn tái nhập về - dùng để cảnh báo');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('import_declaration_id')->nullable()->constrained('import_declarations')->nullOnDelete();
            $table->string('invoice_number', 100)->nullable()->comment('Số hóa đơn');
            $table->decimal('total_value', 15, 2)->nullable()->comment('Tổng trị giá');
            $table->string('currency', 5)->default('USD');
            $table->enum('status', ['active', 'partially_returned', 'fully_returned', 'overdue'])->default('active');
            $table->boolean('alert_sent_30d')->default(false);
            $table->boolean('alert_sent_7d')->default(false);
            $table->string('excel_file_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Bảng chi tiết mặt hàng trong tờ khai tạm xuất
        Schema::create('export_declaration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->cascadeOnDelete();
            $table->foreignId('import_item_id')->constrained('import_declaration_items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->nullable()->comment('Đơn giá tạm xuất');
            $table->decimal('rental_price_per_day', 15, 2)->nullable()->comment('Giá thuê theo ngày');
            $table->string('currency', 5)->default('USD');
            $table->timestamps();
        });

        // Bảng serial theo tờ khai tạm xuất
        Schema::create('export_serial_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->cascadeOnDelete();
            $table->foreignId('serial_id')->constrained('equipment_serials')->cascadeOnDelete();
            $table->date('returned_at')->nullable()->comment('Ngày tái nhập về, NULL = chưa tái nhập');
            $table->text('condition_on_return')->nullable()->comment('Tình trạng khi trả về');
            $table->timestamps();
        });

        // Thêm FK cho equipment_serials.current_export_id sau khi tạo bảng export_declarations
        Schema::table('equipment_serials', function (Blueprint $table) {
            $table->foreign('current_export_id')->references('id')->on('export_declarations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('equipment_serials', function (Blueprint $table) {
            $table->dropForeign(['current_export_id']);
        });
        Schema::dropIfExists('export_serial_items');
        Schema::dropIfExists('export_declaration_items');
        Schema::dropIfExists('export_declarations');
    }
};
