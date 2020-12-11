<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physicalUnits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unitDefinition_id');
            $table->string('unitID',40);
            $table->string('unitType',100)->default(null);
            $table->boolean('available')->default(1);
            $table->unsignedBigInteger('administrator');
            $table->timestamps();
            $table->foreign('unitDefinition_id')->references('id')->on('unitDefinitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physicalUnits');
    }
}
