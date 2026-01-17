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
        Schema::create('motions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assembly_session_id')->constrained('assembly_sessions')->onDelete('cascade');
            $table->text('text');
            $table->enum('status', ['proposed', 'approved', 'rejected'])->default('proposed');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assembly_session_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motions');
    }
};
