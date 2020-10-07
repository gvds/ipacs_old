<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatafilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datafiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('site_id');
            $table->unsignedInteger('fileset');
            $table->string('resource',100);
            $table->string('filename',100);
            $table->date('generationDate');
            $table->string('lab',100);
            $table->string('platform',100);
            $table->string('opperator',100);
            $table->text('description')->nullable();
            $table->string('hash',100);
            $table->unsignedBigInteger('filesize');
            $table->string('filetype',40);
            $table->string('software',40);
            $table->string('owner',60);
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
        Schema::dropIfExists('datafiles');
    }
}
