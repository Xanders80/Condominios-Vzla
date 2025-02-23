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
        Schema::create('access_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('access_group_id')->nullable()->constrained('access_groups')->onDelete('cascade');
            $table->foreignUuid('menu_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->json('access')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_menus');
    }
};
