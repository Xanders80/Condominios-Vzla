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
        Schema::table('common_areas', function (Blueprint $table) {
            $table->enum('pricing_type', ['fixed', 'hourly'])->default('fixed')->after('booking_fee');
            $table->enum('currency', ['USD', 'BS'])->default('USD')->after('pricing_type');
            $table->integer('min_anticipation_hours')->default(24)->after('max_occupancy');
            $table->integer('max_booking_hours')->nullable()->after('min_anticipation_hours');
            $table->decimal('cancellation_penalty_percentage', 5, 2)->default(0)->after('max_booking_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_areas', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_type',
                'currency',
                'min_anticipation_hours',
                'max_booking_hours',
                'cancellation_penalty_percentage'
            ]);
        });
    }
};
