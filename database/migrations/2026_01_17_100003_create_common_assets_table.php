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
        Schema::create('common_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominiums_id')->constrained('condominiums')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable()->comment('piso 1, area social, azotea, etc.');
            $table->enum('status', ['operational', 'maintenance', 'damaged', 'deactivated'])->default('operational');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['condominiums_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('common_assets');
    }
};
