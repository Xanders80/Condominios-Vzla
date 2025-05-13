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
        $this->createUnitTypesTable();
        $this->createDwellerTypesTable();
        $this->createDocumentTypesTable();
        $this->createCondominiumsTable();
        $this->createTowerSectorsTable();
        $this->createFloorStreetsTable();
        $this->createBanksTable();
        $this->createBanksCondominiumTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks_condominium');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('unit_types');
        Schema::dropIfExists('dweller_types');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('floor_streets');
        Schema::dropIfExists('tower_sectors');
        Schema::dropIfExists('condominiums');
    }

    private function createUnitTypesTable(): void
    {
        Schema::create('unit_types', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del tipo de unidad.');

            $table->string('name', self::STRING_LENGTH)->unique()->comment('Nombre del tipo de unidad.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createDwellerTypesTable(): void
    {
        Schema::create('dweller_types', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del tipo de residente.');

            $table->string('name', self::STRING_LENGTH)->unique()->comment('Nombre del tipo de residente.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createDocumentTypesTable(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del tipo de documento.');

            $table->string('name', self::STRING_LENGTH)->unique()->comment('Nombre del tipo de documento.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createCondominiumsTable(): void
    {
        Schema::create('condominiums', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del condominio.');

            $table->string('name', self::STRING_LENGTH)->unique()->nullable()->comment('Nombre del condominio.');
            $table->string('name_incharge', self::STRING_LENGTH)->nullable()->comment('Nombre del encargado del condominio.');
            $table->string('jobs_incharge', self::STRING_LENGTH)->nullable()->comment('Cargo del encargado del condominio.');
            $table->string('email', 150)->unique()->nullable()->comment('Correo electrónico del condominio.');
            $table->string('rif', 15)->unique()->nullable()->comment('RIF del condominio.');
            $table->string('phone', 15)->nullable()->comment('Número de teléfono del condominio.');
            $table->string('address_line', self::STRING_LENGTH)->nullable()->comment('Dirección del condominio.');

            $table->integer('postal_code_address')->nullable()->constrained('postal_zone')->onDelete('set null')->comment('Identificador del código postal del condominio.');

            $table->unsignedInteger('reserve_found')->nullable()->comment('Fondo de reserva del condominio.');
            $table->unsignedInteger('rate_percentage')->nullable()->comment('Porcentaje de tasa del condominio.');
            $table->unsignedInteger('billing_date')->nullable()->comment('Fecha de facturación del condominio.');
            $table->boolean('active')->default(false)->comment('Estado del condominio (activo/inactivo).');
            $table->string('observations', self::TEXT_LENGTH)->nullable()->comment('Observaciones del condominio.');
            $table->string('logo', self::STRING_LENGTH)->nullable()->comment('Logo del condominio.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createTowerSectorsTable(): void
    {
        Schema::create('tower_sectors', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único de la torre o sector.');

            $table->string('name', self::STRING_LENGTH)->unique()->comment('Nombre de la torre o sector.');
            $table->string('description', self::TEXT_LENGTH)->nullable()->comment('Descripción de la torre o sector.');
            $table->foreignUuid('condominiums_id')->nullable()->constrained('condominiums')->comment('Identificador del condominio al que pertenece la torre o sector.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createFloorStreetsTable(): void
    {
        Schema::create('floor_streets', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del piso o calle.');

            $table->string('name', self::STRING_LENGTH)->unique()->comment('Nombre del piso o calle.');
            $table->foreignUuid('tower_sector_id')->nullable()->constrained('tower_sectors')->comment('Identificador de la torre o sector al que pertenece el piso o calle.');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createBanksTable(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único del banco.');

            $table->string('code_sudebank', 4)->nullable()->comment('Código del banco en la SUDEBAN.');
            $table->string('name_ibp', self::STRING_LENGTH)->nullable()->comment('Nombre del banco en el IBP.');
            $table->string('rif', 15)->nullable()->comment('RIF del banco.');
            $table->string('website', self::STRING_LENGTH)->nullable()->comment('Sitio web del banco.');
            $table->boolean('active')->default(false)->nullable()->comment('Estado del banco (activo/inactivo).');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function createBanksCondominiumTable(): void
    {
        Schema::create('banks_condominium', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador único de la relación banco-condominio.');

            $table->string('account_number', 23)->nullable()->comment('Número de cuenta bancaria del condominio.');
            $table->foreignUuid('condominiums_id')->nullable()->constrained('condominiums')->comment('Identificador del condominio.');
            $table->foreignUuid('banks_id')->nullable()->constrained('banks')->comment('Identificador del banco.');

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
