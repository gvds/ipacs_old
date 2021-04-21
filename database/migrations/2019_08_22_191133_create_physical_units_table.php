<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysicalUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physicalUnits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unitDefinition_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('unitID', 40);
            $table->string('unitType', 100)->nullable();
            $table->boolean('available')->default(1);
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
        Schema::dropIfExists('physical_units');
    }
}
