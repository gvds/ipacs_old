<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->unsigned();
            $table->tinyInteger('fieldorder')->unsigned();
            $table->string('fieldname',25);
            $table->string('fieldtitle',25)->nullable();
            $table->tinyInteger('fieldlength')->unsigned()->default(0);
            $table->timestamps();
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_fields');
    }
}
