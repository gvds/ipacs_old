<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manifest_id');
            $table->unsignedBigInteger('event_sample_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('prior_samplestatus_id');
            $table->boolean('received')->default(0);
            $table->dateTime('receivedTime')-> nullable();
            $table->timestamps();

            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('event_sample_id')->references('id')->on('event_sample')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manifest_items');
    }
}
