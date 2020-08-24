<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSampleStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampleStatus', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->text('samplestatus',25);
        });
        DB::unprepared("INSERT INTO `sampleStatus` (`id`, `samplestatus`) VALUES
        (0, 'Unassigned'),
        (1, 'Registered'),
        (2, 'Logged'),
        (3, 'In Storage'),
        (4, 'PreTransfer'),
        (5, 'Used'),
        (6, 'Reassigned'),
        (7, 'Transferred'),
        (8, 'Lost'),
        (9, 'LoggedOut'),
        (10, 'Received')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sampleStatus');
    }
}
