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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignUuid('common_area_id')->nullable()->constrained('common_areas')->onDelete('set null');
            $table->foreignUuid('common_asset_id')->nullable()->constrained('common_assets')->onDelete('set null');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['reported', 'investigating', 'confirmed', 'rejected', 'resolved'])->default('reported');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index(['common_area_id', 'status']);
            $table->index(['common_asset_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
