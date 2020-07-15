<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_types', function (Blueprint $table) {
            $table->bigIncrements('sampletype_id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('sampletype');
            $table->boolean('primary')->default(1);
            $table->tinyInteger('aliquots')->default(0);
            $table->boolean('pooled')->dafault(0);
            $table->float('defaultVolume')->unsigned()->nullable();
            $table->string('volumenUnit',10)->default('NA');
            $table->boolean('active')->default(1);
            $table->boolean('store')->default(0);
            $table->string('transferDestination',25)->nullable();
            $table->string('transferSource',15)->nullable();
            $table->string('sampleGroup',15)->nullable();
            $table->string('tubeLabelType',15)->nullable();
            $table->string('storageSampletype',50)->nullable();
            $table->enum('parentType',['primary','derivative'])->default('primary');
            $table->tinyInteger('parentSampletype_id')->unsigned()->nullable();
            $table->string('xtra_attribs',255)->nullable();
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
        Schema::dropIfExists('sample_types');
    }
}
