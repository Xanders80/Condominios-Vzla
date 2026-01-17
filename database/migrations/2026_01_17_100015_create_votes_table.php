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
        Schema::create('votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('motion_id')->constrained('motions')->onDelete('cascade');
            $table->foreignUuid('unit_id')->constrained('units')->onDelete('cascade');
            $table->enum('vote', ['yes', 'no', 'abstain']);
            $table->timestamps();

            $table->unique(['motion_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
