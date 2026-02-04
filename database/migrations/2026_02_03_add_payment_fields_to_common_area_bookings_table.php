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
        Schema::table('common_area_bookings', function (Blueprint $table) {
            $table->decimal('total_amount', 12, 2)->default(0)->after('end_time');
            $table->string('currency', 3)->default('USD')->after('total_amount');
            $table->decimal('exchange_rate', 12, 4)->nullable()->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_area_bookings', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'currency', 'exchange_rate']);
        });
    }
};
