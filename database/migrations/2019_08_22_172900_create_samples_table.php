<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->bigIncrements('sample_id');
            $table->string('barcode',20);
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('event_id')->unsigned()->nullable();
            $table->string('site',15)->nullable();
            $table->tinyInteger('sampletype_id')->unsigned()->nullable();
            $table->tinyInteger('samplestatus_id')->unsigned()->default(0);
            $table->integer('location')->unsigned()->nullable();
            $table->string('labelType',15);
            $table->float('volume',8,2)->unsigned()->nullable();
            $table->string('volumeUnit',10)->nullable();
            $table->bigInteger('loggedBy')->unsigned()->nullable();
            $table->dateTime('logTime')->nullable();
            $table->bigInteger('usedBy')->unsigned()->nullable();
            $table->dateTime('usedTime')->nullable();
            $table->tinyInteger('aliquot')->unsigned()->nullable();
            $table->string('parentBarcode',20)->nullable();
            $table->timestamps();
            $table->foreign('sample_id')->references('event_id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('samples');
    }
}
