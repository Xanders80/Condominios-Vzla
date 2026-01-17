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
        Schema::create('interest_calculations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('debt_id')->constrained('debts')->onDelete('cascade');
            $table->decimal('interest_amount', 12, 2);
            $table->decimal('cumulative_capital', 12, 2);
            $table->date('calculation_date');
            $table->decimal('rate_applied', 5, 2);
            $table->timestamps();

            $table->index(['debt_id', 'calculation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_calculations');
    }
};
