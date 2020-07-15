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
            $table->bigIncrements('event_id');
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->string('subjectID',10);
            $table->integer('event')->unsigned();
            $table->tinyInteger('eventstatus_id')->unsigned()->default(0);
            $table->dateTime('reg_datestamp')->nullable();
            $table->dateTime('log_datestamp')->nullable();
            $table->timestamps();
            $table->foreign('subject_id')->references('subject_id')->on('subjects');
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
