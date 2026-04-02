<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng tờ khai tạm nhập (header)
        Schema::create('import_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('declaration_number', 20)->unique()->comment('Số tờ khai');
            $table->string('first_declaration_ref', 20)->nullable()->comment('Số tờ khai đầu tiên');
            $table->string('inspection_code', 10)->nullable()->comment('Mã phân loại kiểm tra, vd: 3D');
            $table->string('customs_type_code', 30)->nullable()->comment('Mã loại hình, vd: G12');
            $table->string('customs_office', 50)->nullable()->comment('Tên CQ Hải quan tiếp nhận');
            $table->dateTime('registration_date')->comment('Ngày đăng ký');
            $table->date('expiry_date')->comment('Thời hạn tái nhập/tái xuất - dùng để cảnh báo');
            $table->string('importer_code', 20)->nullable()->comment('Mã người nhập khẩu');
            $table->string('importer_name', 200)->comment('Tên người nhập khẩu');
            $table->string('exporter_name', 200)->nullable()->comment('Tên người xuất khẩu');
            $table->string('exporter_country', 10)->nullable()->comment('Nước xuất xứ/xuất khẩu, vd: KR');
            $table->string('bill_of_lading', 100)->nullable()->comment('Số vận đơn');
            $table->integer('package_quantity')->nullable()->comment('Số lượng kiện');
            $table->string('package_unit', 10)->nullable()->comment('Đơn vị kiện, vd: PK');
            $table->decimal('gross_weight', 12, 3)->nullable()->comment('Tổng trọng lượng');
            $table->string('weight_unit', 10)->nullable()->comment('Đơn vị trọng lượng, vd: KGM');
            $table->string('invoice_number', 100)->nullable()->comment('Số hóa đơn');
            $table->string('invoice_currency', 5)->nullable()->comment('Đơn vị tiền tệ, vd: USD');
            $table->decimal('invoice_total_value', 15, 2)->nullable()->comment('Tổng trị giá hóa đơn');
            $table->enum('status', ['active', 'extended', 're_exported', 'expired'])->default('active');
            $table->boolean('alert_sent_30d')->default(false)->comment('Đã gửi cảnh báo 30 ngày');
            $table->boolean('alert_sent_7d')->default(false)->comment('Đã gửi cảnh báo 7 ngày');
            $table->string('excel_file_path', 500)->nullable()->comment('Đường dẫn file Excel gốc');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_declarations');
    }
};
