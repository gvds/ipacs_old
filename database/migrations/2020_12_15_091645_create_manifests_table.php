<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->date('shippedDate')->nullable();
            $table->unsignedBigInteger('received_user_id')->nullable();
            $table->date('receivedDate')->nullable();
            $table->unsignedTinyInteger('manifestStatus_id')->default(1);
            $table->unsignedBigInteger('destinationSite_id');
            $table->unsignedBigInteger('sourceSite_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('destinationSite_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('sourceSite_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manifests');
    }
}
