<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_reports', function (Blueprint $table) {
            $table->bigIncrements('storageReport_id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('loggedBy',15);
            $table->bigInteger('sample_id')->unsigned();
            $table->bigInteger('location_id')->unsigned()->nullable();
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
        Schema::dropIfExists('storage_reports');
    }
}
