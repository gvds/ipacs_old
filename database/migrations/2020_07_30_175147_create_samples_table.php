<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('name',50)->nullable();
            $table->boolean('primary')->default(0);
            $table->tinyInteger('aliquots')->unsigned();
            $table->boolean('pooled')->default(0);
            $table->decimal('default_volume',8,2)->nullable();
            $table->text('volume_unit',10)->nullable();
            $table->boolean('store')->default(0);
            $table->text('transferDestination',50)->nullable();
            $table->text('sampleGroup',50)->nullable();
            $table->tinyInteger('tubeLabelType_id');
            $table->text('storageSampleType',50)->nullable();
            $table->unsignedBigInteger('parentSampleType_id')->nullable();
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
        Schema::dropIfExists('arms');
    }
}
