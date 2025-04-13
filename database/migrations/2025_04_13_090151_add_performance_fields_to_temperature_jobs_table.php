<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_performance_fields_to_temperature_jobs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temperature_jobs', function (Blueprint $table) {
            $table->float('execution_time')->nullable()->after('status');
            $table->integer('memory_usage')->nullable()->after('execution_time');
            $table->timestamp('completed_at')->nullable()->after('memory_usage');
        });
    }

    public function down(): void
    {
        Schema::table('temperature_jobs', function (Blueprint $table) {
            $table->dropColumn(['execution_time', 'memory_usage', 'completed_at']);
        });
    }
};
