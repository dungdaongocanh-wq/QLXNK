<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            // Kiểm tra từng cột trước khi thêm để tránh lỗi trùng
            if (!Schema::hasColumn('import_declarations', 'invoice_date')) {
                $table->string('invoice_date', 50)->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('import_declarations', 'total_invoice_value')) {
                $table->decimal('total_invoice_value', 15, 2)->nullable()->after('invoice_date');
            }
            if (!Schema::hasColumn('import_declarations', 'currency')) {
                $table->string('currency', 5)->nullable()->default('USD')->after('total_invoice_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            $table->dropColumn(['invoice_date', 'total_invoice_value', 'currency']);
        });
    }
};