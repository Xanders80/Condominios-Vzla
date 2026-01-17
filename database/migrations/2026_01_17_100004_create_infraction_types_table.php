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
        Schema::create('infraction_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominiums_id')->constrained('condominiums')->onDelete('cascade');
            $table->string('name');
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->text('legal_basis')->nullable()->comment('Ref. Reglamento interno o ley');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['condominiums_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infraction_types');
    }
};
