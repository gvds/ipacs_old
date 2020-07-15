<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarcodeRegLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcode_reg_log', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->bigInteger('project_id')->unsigned();
            $table->string('tubeLabelType',15);
            $table->string('site',15)->nullable();
            $table->string('prefix',10);
            $table->tinyInteger('digits')->unsigned();
            $table->bigInteger('start')->unsigned();
            $table->mediumInteger('labelCount')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->mediumInteger('failures')->unsigned();
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
        Schema::dropIfExists('barcode_reg_logs');
    }
}
