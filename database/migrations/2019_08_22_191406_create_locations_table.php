<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('virtualUnit_id')->constrained();
            $table->string('storageProjectName',40);
            $table->string('barcode',20)->nullable();
            $table->unsignedTinyInteger('rack')->nullable();
            $table->string('box')->nullable();
            $table->unsignedSmallInteger('position')->nullable();
            $table->boolean('used')->default(0);
            $table->boolean('virgin')->default(1);
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
        Schema::dropIfExists('locations');
    }
}
