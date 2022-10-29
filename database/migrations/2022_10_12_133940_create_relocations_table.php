<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storageconsolidation_id')->constrained()->onDelete('cascade');
            $table->string('barcode', 20);
            $table->foreignId('source_location_id');
            $table->foreignId('destination_location_id');
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
        Schema::dropIfExists('relocations');
    }
}
