<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Adresses extends Migration
{
    public function up()
    {
        Schema::create('adresses', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('place_id')->unsigned();

            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->string('complement')->nullable();
            $table->string('cep')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adresses');
    }
}
