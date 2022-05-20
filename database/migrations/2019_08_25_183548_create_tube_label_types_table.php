<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTubeLabelTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tube_label_types', function (Blueprint $table) {
            $table->id();
            $table->string('tubeLabelType',15);
            $table->boolean('preregister')->default(0);
            $table->string('barcodeFormat',50);
            $table->set('registration',['range','single'])->default('range');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->timestamps();
        });
        DB::unprepared("INSERT INTO `tube_label_types` (`id`, `tubeLabelType`, `preregister`, `barcodeFormat`, `registration`, `project_id`, `created_at`, `updated_at`) VALUES
            (1, 'Adhesive', 1, '^(A\\d{7}|\\d{4}|[A-Z]\\d{3}[A-Z]\\d{4})$', 'range', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (2, 'FluidX 260ul', 0, '^SU\\d{8}$', 'single', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (3, 'FluidX 500ul', 0, '^(FD|SU)\\d{8}$', 'single', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (4, 'MGIT', 0, '^\\d{12}$', 'single', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (5, 'Pre 2ml', 1, '^(G\\d{9}|E\\d{7}|\\d{8})$', 'range', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (6, 'Pre 5ml', 1, '', 'range', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (7, 'Xpert', 0, '', 'single', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33'),
            (8, 'FluidX 1ml', 0, '^SU\\d{8}$', 'range', NULL, '2021-05-25 11:28:33', '2021-05-25 11:28:33');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tube_label_types');
    }
}
