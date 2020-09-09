<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subjectID',10);
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('site_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('firstname',20)->nullable();
            $table->string('surname',30)->nullable();
            $table->string('address1',50)->nullable();
            $table->string('address2',50)->nullable();
            $table->string('address3',50)->nullable();
            $table->date('enrolDate')->nullable();
            $table->bigInteger('arm_id')->unsigned()->nullable();
            $table->date('armBaselineDate')->nullable();
            $table->bigInteger('previous_arm_id')->unsigned()->nullable();
            $table->date('previousArmBaselineDate')->nullable();
            $table->tinyInteger('subject_status')->unsigned()->default(0);
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
