<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('value');
            $table->string('status');
            $table->double('price');
            $table->integer('ticket_id');
            $table->integer('room_id');
            $table->integer('schedule_id');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat');
    }
}
