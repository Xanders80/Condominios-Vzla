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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('receipt_concepts');
        Schema::dropIfExists('receipts');
        Schema::enableForeignKeyConstraints();

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('unit_id')->constrained('units')->onDelete('restrict');
            $table->date('issue_date')->index();
            $table->date('due_date')->index();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('coownership_coefficient', 5, 4)->comment('Coefficient applied at time of issuance');
            $table->enum('status', ['pending', 'partial', 'paid', 'canceled'])->default('pending')->index();
            $table->string('receipt_number', 50)->unique();
            $table->json('concepts_breakdown')->nullable()->comment('Redundant breakdown for fast views (Art. 38 LPH)');
            $table->string('qr_verification_hash', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['unit_id', 'status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
