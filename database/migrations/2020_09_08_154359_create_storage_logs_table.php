<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('storageReport_id');
            $table->unsignedBigInteger('sampletype_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('sample_id');
            $table->timestamps();
            $table->foreign('storageReport_id')->references('id')->on('storage_reports')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_logs');
    }
}
