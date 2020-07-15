<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PhysicalUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physicalUnits', function (Blueprint $table) {
            $table->string('unitID',40)->primary();
            $table->string('unitType',100);
            $table->boolean('available')->default(1);
            $table->string('administrator',20);
            $table->timestamps();
            $table->foreign('unitType')->references('unitType')->on('unitDefinitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physicalUnits');
    }
}
