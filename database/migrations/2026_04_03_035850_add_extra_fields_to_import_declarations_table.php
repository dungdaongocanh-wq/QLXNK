<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            $table->string('customs_detail_value', 500)->nullable()->after('invoice_total_value')->comment('Chi tiết khai trị giá D64');
            $table->text('import_notes')->nullable()->after('customs_detail_value')->comment('Ghi chú G85');
        });
    }

    public function down(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            $table->dropColumn(['customs_detail_value', 'import_notes']);
        });
    }
};