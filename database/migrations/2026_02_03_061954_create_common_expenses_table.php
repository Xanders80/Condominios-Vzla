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
        Schema::dropIfExists('common_expenses');
        if (Schema::hasColumn('receipts', 'common_expense_id')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->dropColumn('common_expense_id');
            });
        }
        Schema::enableForeignKeyConstraints();

        Schema::create('common_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('condominium_id')->constrained('condominiums')->onDelete('cascade');
            $table->date('period')->index()->comment('Target month/year for the expenses');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'canceled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominium_id', 'period']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->foreignId('common_expense_id')->nullable()->after('id')->constrained('common_expenses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['common_expense_id']);
            $table->dropColumn('common_expense_id');
        });
        Schema::dropIfExists('common_expenses');
    }
};
