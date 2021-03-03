<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Messages extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->integer('responsible_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('responsible_id')->references('id')->on('responsibles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->text('message');
            $table->boolean('from_responsible')->default(false);
            $table->boolean('read')->default(false);
            $table->dateTime('received')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
