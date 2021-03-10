<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTubeLabelTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tube_label_types', function (Blueprint $table) {
            $table->id();
            $table->string('tubeLabelType',15);
            $table->boolean('preregister')->default(0);
            $table->string('barcodeFormat',30);
            $table->set('registration',['range','single'])->default('range');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tube_label_types');
    }
}
