<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('unitDefinition_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('section')->unsigned()->default(1);
            $table->tinyInteger('rows')->unsigned()->default(1);
            $table->tinyInteger('columns')->unsigned()->default(1);
            $table->tinyInteger('boxes')->unsigned()->default(0);
            $table->tinyInteger('positions')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
