<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Times extends Migration
{
    public function up()
    {
        Schema::create('times', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('place_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('name')->nullable();
            $table->text('details')->nullable();
            $table->date('selected_date');
            $table->time('start');
            $table->time('finish');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('times');
    }
}
