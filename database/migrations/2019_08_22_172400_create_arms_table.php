<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('redcap_arm_id')->unsigned()->nullable();
            $table->tinyInteger('arm_num')->unsigned()->nullable();
            $table->boolean('manual_enrol')->default(0);
            $table->string('switcharms',100)->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->unique(['project_id','arm_num'],'project_id_arm_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arms');
    }
}
