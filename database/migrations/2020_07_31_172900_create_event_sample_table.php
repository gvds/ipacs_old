<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_sample', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampletype_id');
            $table->bigInteger('event_subject_id')->unsigned();
            $table->string('barcode',20);
            $table->tinyInteger('aliquot')->unsigned()->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('samplestatus_id')->default(0);
            $table->unsignedBigInteger('location')->nullable();
            // $table->string('labelType',15);
            $table->float('volume',8,2)->unsigned()->nullable();
            $table->string('volumeUnit',10)->nullable();
            $table->unsignedTinyInteger('thawcount')->default(0);
            $table->unsignedBigInteger('loggedBy')->nullable();
            $table->dateTime('logTime')->nullable();
            $table->unsignedBigInteger('usedBy')->nullable();
            $table->dateTime('usedTime')->nullable();
            $table->string('parentBarcode',20)->nullable();
            $table->timestamps();
            $table->foreign('sampletype_id')->references('id')->on('sampletypes')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('event_subject_id')->references('id')->on('event_subject')->onDelete('cascade')->onUpdate('cascade');
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
