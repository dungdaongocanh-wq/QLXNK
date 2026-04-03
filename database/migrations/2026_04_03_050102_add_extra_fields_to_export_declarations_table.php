<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_declarations', function (Blueprint $table) {
            $table->string('inspection_code', 10)->nullable()->after('customs_type_code')->comment('Mã PL kiểm tra F6');
            $table->string('exporter_name', 200)->nullable()->after('customs_office')->comment('Tên công ty xuất F14');
            $table->string('exporter_tax_code', 50)->nullable()->after('exporter_name')->comment('MST F13');
            $table->string('importer_name', 200)->nullable()->after('exporter_tax_code')->comment('Tên công ty nhập F30');
            $table->string('importer_address', 500)->nullable()->after('importer_name')->comment('Địa chỉ F33');
            $table->string('importer_country', 10)->nullable()->after('importer_address')->comment('Mã nước F35');
            $table->integer('package_quantity')->nullable()->after('importer_country')->comment('Số lượng kiện H40');
            $table->string('package_unit', 10)->nullable()->after('package_quantity');
            $table->decimal('gross_weight', 12, 3)->nullable()->after('package_unit')->comment('Tổng trọng lượng H41');
            $table->string('weight_unit', 10)->nullable()->after('gross_weight');
            $table->string('marks_and_numbers', 500)->nullable()->after('weight_unit')->comment('Ký hiệu và số hiệu H47');
            $table->text('export_notes')->nullable()->after('marks_and_numbers')->comment('Ghi chú F64');
            $table->string('invoice_date', 50)->nullable()->after('invoice_number')->comment('Ngày phát hành S51');
            $table->integer('total_item_lines')->nullable()->after('total_value')->comment('Tổng số dòng hàng');
        });
    }

    public function down(): void
    {
        Schema::table('export_declarations', function (Blueprint $table) {
            $table->dropColumn([
                'inspection_code', 'exporter_name', 'exporter_tax_code',
                'importer_name', 'importer_address', 'importer_country',
                'package_quantity', 'package_unit', 'gross_weight', 'weight_unit',
                'marks_and_numbers', 'export_notes', 'invoice_date', 'total_item_lines',
            ]);
        });
    }
};