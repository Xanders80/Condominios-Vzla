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
        Schema::create('coownership_coefficients', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('coefficient', 5, 4)->comment('Art. 6 LPH: Proportion based on property rights');
            $table->date('start_date');
            $table->timestamps();

            $table->index(['unit_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coownership_coefficients');
    }
};
