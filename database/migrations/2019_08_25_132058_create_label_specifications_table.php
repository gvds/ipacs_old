<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_specifications', function (Blueprint $table) {
            $table->string('label_id',40)->primary();
            $table->string('paper_size',10);
            $table->string('metric',10);
            $table->float('margin_left')->unsigned();
            $table->float('margin_top')->unsigned();
            $table->tinyInteger('NX')->unsigned();
            $table->tinyInteger('NY')->unsigned();
            $table->float('SpaceX')->unsigned();
            $table->float('SpaceY')->unsigned();
            $table->float('width')->unsigned();
            $table->float('height')->unsigned();
            $table->tinyInteger('font_size')->unsigned();
            $table->tinyInteger('padding')->unsigned()->nullable();
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
        Schema::dropIfExists('label_specifications');
    }
}
