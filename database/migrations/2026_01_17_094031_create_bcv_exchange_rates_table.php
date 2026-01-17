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
        Schema::create('bcv_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('rate_date')->unique();
            $table->decimal('official_rate', 10, 4)->comment('BCV Official Rate');
            $table->decimal('parallel_rate', 10, 4)->comment('Market/Parallel Rate');
            $table->decimal('used_for_calculations', 10, 4);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['rate_date', 'used_for_calculations']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bcv_exchange_rates');
    }
};
