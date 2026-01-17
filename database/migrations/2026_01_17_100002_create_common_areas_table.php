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
        Schema::create('common_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominiums_id')->constrained('condominiums')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('booking_fee', 12, 2)->default(0);
            $table->integer('max_occupancy')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['condominiums_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('common_areas');
    }
};
