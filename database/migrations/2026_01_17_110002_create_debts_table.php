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
        Schema::create('debts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('unit_id')->constrained('units')->onDelete('cascade');
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['current', 'pre_delinquent', 'delinquent', 'legal_action', 'paid'])->default('current');
            $table->date('due_date');
            $table->integer('grace_period_days')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
