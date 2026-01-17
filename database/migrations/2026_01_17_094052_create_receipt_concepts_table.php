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
        Schema::dropIfExists('receipt_concepts');
        Schema::create('receipt_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->onDelete('cascade');
            $table->string('concept_name', 100)->comment('mantenimiento, reserva, extraordinario, multa');
            $table->decimal('amount', 12, 2);
            $table->decimal('coefficient_applied', 5, 4);
            $table->text('description')->nullable();
            $table->integer('legal_basis_article')->nullable()->comment('LPH Article reference');
            $table->timestamps();

            $table->index(['receipt_id', 'concept_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_concepts');
    }
};
