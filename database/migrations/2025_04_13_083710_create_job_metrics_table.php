<?php
// database/migrations/xxxx_xx_xx_create_job_metrics_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('job_id')->unique(); // ارتباط بـ temperature_jobs
            $table->double('execution_time')->nullable(); // الزمن بالثواني
            $table->integer('memory_usage')->nullable(); // الكيلوبايت
            $table->integer('processed_rows')->nullable(); // عدد الصفوف المعالجة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_metrics');
    }
};
