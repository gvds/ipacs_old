<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_defs', function (Blueprint $table) {
            $table->bigIncrements('eventDef_id');
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('armDef_id')->unsigned();
            $table->string('event_description',50)->nullable();
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
            $table->foreign('armDef_id')->references('armDef_id')->on('arm_defs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_defs');
    }
}
