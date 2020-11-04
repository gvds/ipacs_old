<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirtualUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualUnits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('virtualUnit',50);
            $table->string('physicalUnit_id',40);
            $table->unsignedBigInteger('project_id');
            $table->string('project',50);
            $table->unsignedTinyInteger('section');
            $table->unsignedTinyInteger('startRack',3)->nullable();
            $table->unsignedTinyInteger('endRack',3)->nullable();
            $table->string('startBox',3)->nullable();
            $table->string('endBox',3)->nullable();
            $table->string('storageSampleType',50)->nullable();
            $table->unsignedSmallInteger('boxCapacity')->nullable();
            $table->unsignedSmallInteger('rackCapacity')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('physicalUnit_id')->references('id')->on('physicalUnits');
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
