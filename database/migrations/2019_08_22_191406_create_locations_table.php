<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('virtualUnit_id');
            $table->string('storageProjectName',40);
            $table->string('barcode',20);
            $table->tinyInteger('rack')->unsigned()->nullable();
            $table->string('box')->nullable();
            $table->smallInteger('position')->unsigned()->nullable();
            $table->boolean('used')->default(0);
            $table->boolean('virgin')->default(1);
            $table->timestamps();
            $table->foreign('virtualUnit_id')->references('id')->on('virtualUnits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
