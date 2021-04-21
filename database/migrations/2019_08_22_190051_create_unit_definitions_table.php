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
            $table->id();
            $table->string('unitType',100);
            $table->unsignedTinyInteger('section_count')->default(0);
            $table->unsignedsmallInteger('racks')->nullable();
            $table->unsignedsmallInteger('boxes')->nullable();
            $table->string('sectionLayout',10)->nullable();
            $table->string('boxDesignation',10)->nullable();
            $table->string('storageType',20)->nullable();
            $table->string('rackOrder',15)->nullable();
            $table->string('orientation',10)->nullable();
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
