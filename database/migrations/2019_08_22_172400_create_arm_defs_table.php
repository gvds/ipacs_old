<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArmDefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arm_defs', function (Blueprint $table) {
            $table->bigIncrements('armDef_id');
            $table->string('description',50)->nullable();
            $table->bigInteger('project_id')->unsigned();
            $table->tinyInteger('arm_num')->unsigned()->nullable();
            $table->boolean('manual_enrole')->default(1);
            $table->string('switcharms',25)->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('project_id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arm_defs');
    }
}
