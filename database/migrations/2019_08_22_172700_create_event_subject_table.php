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
            $table->tinyInteger('itteration')->unsigned()->default(1);
            $table->tinyInteger('eventstatus_id')->unsigned()->default(0);
            $table->dateTime('reg_timestamp')->nullable();
            $table->dateTime('log_timestamp')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
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
