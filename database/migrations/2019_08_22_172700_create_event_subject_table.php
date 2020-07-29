<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_subject', function (Blueprint $table) {
            $table->bigIncrements('event_id');
            $table->bigInteger('subject_id')->unsigned();
            // $table->bigInteger('project_id')->unsigned();
            $table->string('subjectID',10);
            // $table->integer('event')->unsigned();
            $table->tinyInteger('eventstatus_id')->unsigned()->default(0);
            $table->dateTime('reg_timestamp')->nullable();
            $table->dateTime('log_timestamp')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            // $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_subject');
    }
}
