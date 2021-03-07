<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('virtualUnit', 50);
            $table->unsignedBigInteger('physicalUnit_id');
            $table->unsignedBigInteger('project_id');
            $table->string('project', 50);
            $table->unsignedTinyInteger('section');
            $table->unsignedTinyInteger('startRack')->nullable();
            $table->unsignedTinyInteger('endRack')->nullable();
            $table->string('startBox', 3)->nullable();
            $table->string('endBox', 3)->nullable();
            $table->string('storageSampleType', 50)->nullable();
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
        Schema::dropIfExists('virtual_units');
    }
}
