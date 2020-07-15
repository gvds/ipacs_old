<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VirtualUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualUnits', function (Blueprint $table) {
            $table->bigIncrements('virtualUnit_id');
            $table->string('virtualUnit',50);
            $table->string('unitID',40);
            $table->string('project',50);
            $table->tinyInteger('section')->unsigned();
            $table->string('startRack',3)->nullable();
            $table->string('endRack',3)->nullable();
            $table->string('startBox',3)->nullable();
            $table->string('endBox',3)->nullable();
            $table->string('sampleType',20)->nullable();
            $table->smallInteger('boxCapacity')->unsigned()->nullable();
            $table->smallInteger('rackCapacity')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('unitID')->references('unitID')->on('physicalUnits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtualUnits');
    }
}
