<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('common_area_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('common_area_id')->constrained('common_areas')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['common_area_id', 'day_of_week', 'is_active'], 'idx_ca_schedule_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('common_area_schedules');
    }
};
