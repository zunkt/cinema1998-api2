<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSeatRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat_room', function (Blueprint $table) {
            $table->foreign('room_id', 'seat_room_room_idfk_1')
                ->references('id')
                ->on('room')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seat_room', function (Blueprint $table) {
            $table->dropForeign('seat_room_room_idfk_1');
        });
    }
}
