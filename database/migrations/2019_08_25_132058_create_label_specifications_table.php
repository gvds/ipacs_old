<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLabelSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_specifications', function (Blueprint $table) {
            $table->string('label_id',40)->primary();
            $table->string('paper_size',10);
            $table->string('metric',10);
            $table->float('marginLeft')->unsigned();
            $table->float('marginTop')->unsigned();
            $table->tinyInteger('NX')->unsigned();
            $table->tinyInteger('NY')->unsigned();
            $table->float('SpaceX')->unsigned();
            $table->float('SpaceY')->unsigned();
            $table->float('width')->unsigned();
            $table->float('height')->unsigned();
            $table->tinyInteger('font_size')->unsigned();
            $table->tinyInteger('padding')->unsigned()->nullable();
            $table->timestamps();
        });
        DB::unprepared("insert into label_specifications (`label_id`,`Paper_size`,metric,marginLeft,marginTop,NX,NY,SpaceX,SpaceY,width,height,`font_size`,padding) values
        ('5160','letter','mm',1.762,10.7,3,10,3.175,0,66.675,25.4,8,null),
        ('5161','letter','mm',0.967,10.7,2,10,3.967,0,101.6,25.4,8,null),
        ('5162','letter','mm',0.97,20.224,2,7,4.762,0,100.807,35.72,8,null),
        ('5163','letter','mm',1.762,10.7,2,5,3.175,0,101.6,50.8,8,null),
        ('5164','letter','in',0.148,0.5,2,3,0.2031,0,4,3.33,12,null),
        ('8600','letter','mm',7.1,19,3,10,9.5,3.1,66.6,25.4,8,null),
        ('L7163','A4','mm',5,15,2,7,25,0,99.1,38.1,9,null),
        ('3422','A4','mm',0,8.5,3,8,0,0,70,35,9,null),
        ('L7651','A4','mm',5,11,5,13,2,0,38.1,21.1,9,null),
        ('L7651_mod','A4','mm',5,11,5,13,3.2,0.1,38.1,21.1,8,1)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_specifications');
    }
}
