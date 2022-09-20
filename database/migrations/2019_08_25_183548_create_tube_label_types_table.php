<?php

use App\tubeLabelType;
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
            $table->string('tubeLabelType', 15);
            $table->boolean('preregister')->default(0);
            $table->string('barcodeFormat', 50);
            $table->set('registration', ['range', 'single'])->default('range');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->timestamps();
        });
        tubeLabelType::create([
            'id' => 1,
            'tubeLabelType' => 'Adhesive',
            'preregister' => 1,
            'barcodeFormat' => '^(A\d{7}|\d{4}|[A-Z]\d{3}[A-Z]\d{4})$',
            'registration' => 'range'
        ]);
        tubeLabelType::create([
            'id' => 2,
            'tubeLabelType' => 'FluidX 260ul',
            'preregister' => 0,
            'barcodeFormat' => '^SU\d{8}$',
            'registration' => 'single'
        ]);
        tubeLabelType::create([
            'id' => 3,
            'tubeLabelType' => 'FluidX 500ul',
            'preregister' => 0,
            'barcodeFormat' => '^(FD|SU)\\d{8}$',
            'registration' => 'single'
        ]);
        tubeLabelType::create([
            'id' => 4,
            'tubeLabelType' => 'MGIT',
            'preregister' => 0,
            'barcodeFormat' => '^\d{12}$',
            'registration' => 'single'
        ]);
        tubeLabelType::create([
            'id' => 5,
            'tubeLabelType' => 'Pre 2ml',
            'preregister' => 1,
            'barcodeFormat' => '^(G\d{9}|E\d{7}|\d{8})$',
            'registration' => 'range'
        ]);
        tubeLabelType::create([
            'id' => 6,
            'tubeLabelType' => 'Pre 5ml',
            'preregister' => 0,
            'barcodeFormat' => '',
            'registration' => 'range'
        ]);
        tubeLabelType::create([
            'id' => 7,
            'tubeLabelType' => 'Xpert',
            'preregister' => 0,
            'barcodeFormat' => '',
            'registration' => 'single'
        ]);
        tubeLabelType::create([
            'id' => 8,
            'tubeLabelType' => 'FluidX 1ml',
            'preregister' => 0,
            'barcodeFormat' => '^SU\d{8}$',
            'registration' => 'range'
        ]);
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
