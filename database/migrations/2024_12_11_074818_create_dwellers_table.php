<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const STRING_LENGTH = 100;
    private const TEXT_LENGTH = 512;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createDwellersTable();
        $this->createUnitsTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('dwellers');
    }

    private function createDwellersTable(): void
    {
        Schema::create('dwellers', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del residente.');

            $table->foreignUuid('document_type_id')->nullable()->constrained('document_types')->comment('Identificador del tipo de documento.');
            $table->foreignUuid('dweller_type_id')->nullable()->constrained('dweller_types')->comment('Identificador del tipo de residente.');

            $table->string('first_name', self::STRING_LENGTH)->nullable()->comment('Nombre del residente.');
            $table->string('last_name', self::STRING_LENGTH)->nullable()->comment('Apellido del residente.');
            $table->bigInteger('document_id')->unique()->comment('Número de documento del residente. Debe ser único.');
            $table->string('email', 150)->unique()->comment('Correo electrónico del residente.');
            $table->string('phone_number', 15)->nullable()->comment('Número de teléfono del residente.');
            $table->string('cell_phone_number', 15)->nullable()->comment('Número de celular del residente.');
            $table->string('observations', self::TEXT_LENGTH)->nullable()->comment('Observaciones del residente.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createUnitsTable(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único de la unidad.');

            $table->foreignUuid('unit_type_id')->nullable()->constrained('unit_types')->comment('Identificador del tipo de unidad.');
            $table->foreignUuid('dweller_id')->nullable()->constrained('dwellers')->comment('Identificador del residente asociado a la unidad.');
            $table->foreignUuid('tower_sector_id')->nullable()->constrained('tower_sectors')->comment('Identificador de la torre o sector de la unidad.');
            $table->foreignUuid('floor_street_id')->nullable()->constrained('floor_streets')->comment('Identificador del piso o calle de la unidad.');

            $table->string('name', self::STRING_LENGTH)->comment('Nombre de la unidad.');
            $table->boolean('status')->nullable()->comment('Estado de la unidad.');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
