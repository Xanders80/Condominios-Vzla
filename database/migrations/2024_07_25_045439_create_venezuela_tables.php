<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const STRING_LENGTH = 100;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createStatesTable();
        $this->createMunicipalitiesTable();
        $this->createCitiesTable();
        $this->createParishesTable();
        $this->createPostalZoneTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postal_zone');
        Schema::dropIfExists('parishes');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('full_adddress');
    }

    private function createStatesTable(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->increments('id')->comment('Identificador único del estado.');
            $table->string('name', self::STRING_LENGTH)->comment('Nombre del estado.');
            $table->string('iso_3166_2', 4)->comment('Código ISO 3166-2 del estado.');
        });
    }

    private function createMunicipalitiesTable(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->increments('id')->comment('Identificador único del municipio.');
            $table->integer('state_id')->unsigned()->constrained()->onDelete('cascade')->comment('Identificador del estado al que pertenece el municipio.');
            $table->string('name', self::STRING_LENGTH)->comment('Nombre del municipio.');
            $table->foreign('state_id')->references('id')->on('states');
        });
    }

    private function createCitiesTable(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id')->comment('Identificador único de la ciudad.');
            $table->integer('state_id')->unsigned()->constrained()->onDelete('cascade')->comment('Identificador del estado al que pertenece la ciudad.');
            $table->integer('municipality_id')->unsigned()->constrained()->onDelete('cascade')->comment('Identificador del municipio al que pertenece la ciudad.');
            $table->string('name', self::STRING_LENGTH)->comment('Nombre de la ciudad.');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
        });
    }

    private function createParishesTable(): void
    {
        Schema::create('parishes', function (Blueprint $table) {
            $table->increments('id')->comment('Identificador único de la parroquia.');
            $table->integer('municipality_id')->unsigned()->constrained()->onDelete('cascade')->comment('Identificador del municipio al que pertenece la parroquia.');
            $table->string('name', self::STRING_LENGTH)->comment('Nombre de la parroquia.');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
        });
    }

    private function createPostalZoneTable(): void
    {
        Schema::create('postal_zone', function (Blueprint $table) {
            $table->increments('id')->comment('Identificador único de la zona postal.');
            $table->integer('parish_id')->unsigned()->constrained()->onDelete('cascade')->comment('Identificador de la parroquia a la que pertenece la zona postal.');
            $table->string('name', self::STRING_LENGTH)->comment('Nombre de la zona postal.');
            $table->char('zip_code', 4)->comment('Código postal de la zona.');
            $table->foreign('parish_id')->references('id')->on('parishes');
        });
    }
};
