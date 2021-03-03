<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Phones extends Migration
{
    public function up()
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('place_id')->unsigned();

            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('phone');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('phones');
    }
}
