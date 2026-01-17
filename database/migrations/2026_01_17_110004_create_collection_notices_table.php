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
        Schema::create('collection_notices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('unit_id')->constrained('units')->onDelete('cascade');
            $table->enum('notice_type', ['reminder', 'formal_act', 'legal_prevention']);
            $table->string('content_hash', 64)->comment('SHA256 for legal integrity (Sarraf)');
            $table->string('proof_path')->nullable()->comment('Cloud storage path for proof');
            $table->dateTime('sent_at')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'notice_type', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_notices');
    }
};
