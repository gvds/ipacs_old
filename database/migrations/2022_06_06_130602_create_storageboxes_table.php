<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorageboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storageboxes', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 20)->unique();
            $table->foreignId('sampletype_id');
            $table->unsignedTinyInteger('positions');
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
        Schema::dropIfExists('storageboxes');
    }
}
