<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng thông báo trong hệ thống
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50)->comment('import_expiry | export_expiry');
            $table->string('title', 200)->nullable();
            $table->text('message')->nullable();
            $table->string('related_type', 50)->nullable()->comment('import_declarations | export_declarations');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID của bản ghi liên quan');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Bảng cấu hình cảnh báo
        Schema::create('alert_configs', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type', 50)->comment('import_expiry | export_expiry');
            $table->integer('days_before')->default(30)->comment('Cảnh báo trước N ngày');
            $table->json('notify_emails')->nullable()->comment('Danh sách email nhận cảnh báo');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_configs');
        Schema::dropIfExists('notifications');
    }
};
