<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('arm_id')->unsigned();
            $table->bigInteger('redcap_event_id')->unsigned()->nullable();
            $table->string('name',50)->nullable();
            $table->boolean('autolog')->default(0);
            $table->mediumInteger('offset')->unsigned()->nullable();
            $table->mediumInteger('offset_min')->unsigned()->nullable();
            $table->mediumInteger('offset_max')->unsigned()->nullable();
            $table->tinyInteger('name_labels')->unsigned()->default(0);
            $table->tinyInteger('subject_event_labels')->unsigned()->default(0);
            $table->tinyInteger('study_id_labels')->unsigned()->default(0);
            $table->tinyInteger('event_order')->unsigned()->default(0);
            $table->boolean('active')->default(1);
            $table->timestamps();
            
            $table->foreign('arm_id')->references('id')->on('arms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
