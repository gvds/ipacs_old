<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuManifestStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lu_manifest_status', function (Blueprint $table) {
            $table->id();
            $table->text('manifeststatus',25);
        });
        DB::unprepared("INSERT INTO `lu_manifest_status` (`id`, `manifeststatus`) VALUES
        (1, 'Open'),
        (2, 'Shipped'),
        (3, 'Received')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lu_manifest_status');
    }
}
