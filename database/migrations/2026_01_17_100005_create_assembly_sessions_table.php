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
        Schema::create('assembly_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominiums_id')->constrained('condominiums')->onDelete('cascade');
            $table->string('title');
            $table->text('agenda')->nullable();
            $table->dateTime('session_date');
            $table->enum('status', ['scheduled', 'in_progress', 'finished', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['condominiums_id', 'session_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assembly_sessions');
    }
};
