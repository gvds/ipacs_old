<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtualUnits', function (Blueprint $table) {
            $table->id();
            $table->string('virtualUnit', 50);
            $table->foreignId('physicalUnit_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('project_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project', 50);
            $table->unsignedTinyInteger('section');
            $table->unsignedTinyInteger('startRack');
            $table->unsignedTinyInteger('endRack');
            $table->string('startBox', 3)->nullable();
            $table->string('endBox', 3)->nullable();
            $table->string('storageSampleType', 50)->nullable();
            $table->unsignedSmallInteger('boxCapacity')->nullable();
            $table->unsignedSmallInteger('rackCapacity')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('virtual_units');
    }
}
