<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unitDefinitions', function (Blueprint $table) {
            $table->string('unitType',100)->primary();
            $table->tinyInteger('sections')->unsigned()->default(1);
            $table->smallInteger('racks')->unsigned()->nullable();
            $table->smallInteger('boxes')->unsigned()->nullable();
            $table->string('sectionDesignation',10)->nullable();
            $table->string('sectionLayout',10)->nullable();
            $table->string('rackDesignation',10)->nullable();
            $table->string('boxDesignation',10)->nullable();
            $table->string('rackType',10)->nullable();
            $table->string('storageType',20)->nullable();
            $table->string('rackOrder',10)->nullable();
            $table->char('orientation',1)->nullable();
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
        Schema::dropIfExists('unitDefinitions');
    }
}
