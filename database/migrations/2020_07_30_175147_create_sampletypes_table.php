<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSampleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampletypes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('name',50)->nullable();
            $table->boolean('primary')->default(0);
            $table->tinyInteger('aliquots')->unsigned();
            $table->boolean('pooled')->default(0);
            $table->decimal('defaultVolume',8,2)->nullable();
            $table->string('volumeUnit',10)->nullable();
            $table->boolean('store')->default(0);
            $table->string('transferDestination',50)->nullable();
            $table->string('sampleGroup',50)->nullable();
            $table->unsignedBigInteger('tubeLabelType_id');
            $table->string('storageDestination',10)->nullable();
            $table->string('storageSampleType',50)->nullable()->index();
            $table->unsignedBigInteger('parentSampleType_id')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sampletypes');
    }
}
