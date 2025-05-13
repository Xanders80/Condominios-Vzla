<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createWaysToPaysTable();
        $this->createPaymentsTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('ways_to_pays');
    }

    private function createWaysToPaysTable(): void
    {
        Schema::create('ways_to_pays', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único de la forma de pago.');
            $table->string('name', 100)->unique()->comment('Nombre de la forma de pago.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createPaymentsTable(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del pago.');

            $table->foreignUuid('dweller_id')->nullable()->constrained('dwellers')->comment('Identificador del residente que realizó el pago.');
            $table->foreignUuid('banks_id')->nullable()->constrained('banks')->comment('Identificador del banco al que se realizó el pago.');
            $table->foreignUuid('condominiums_id')->nullable()->constrained('condominiums')->comment('Identificador del condominio al que pertenece el pago.');
            $table->foreignUuid('ways_to_pays_id')->nullable()->constrained('ways_to_pays')->comment('Identificador de la forma de pago.');

            $table->string('nro_confirmation', 50)->unique()->nullable()->comment('Número de confirmación del pago.');
            $table->decimal('amount', 10, 2)->nullable()->comment('Monto del pago.');
            $table->date('date_pay')->nullable()->comment('Fecha en que se realizó el pago.');
            $table->date('date_confirm')->nullable()->comment('Fecha en que se confirmó el pago.');
            $table->boolean('conciliated')->default(false)->comment('Indica si el pago fue conciliado.');
            $table->string('observations', 512)->nullable()->comment('Observaciones del pago.');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
