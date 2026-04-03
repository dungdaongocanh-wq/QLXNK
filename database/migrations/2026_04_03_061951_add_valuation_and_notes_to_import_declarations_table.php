<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            if (!Schema::hasColumn('import_declarations', 'valuation_details')) {
                $table->text('valuation_details')->nullable()->comment('Chi tiết khai trị giá D64');
            }
            if (!Schema::hasColumn('import_declarations', 'import_notes')) {
                $table->text('import_notes')->nullable()->comment('Ghi chú G85');
            }
        });
    }

    public function down(): void
    {
        Schema::table('import_declarations', function (Blueprint $table) {
            $table->dropColumn(['valuation_details', 'import_notes']);
        });
    }
};