<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSeatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat', function (Blueprint $table) {
            $table->foreign('ticket_id', 'seat_ticket_idfk_1')
                ->references('id')
                ->on('ticket')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->foreign('room_id', 'seat_room_idfk_1')
                ->references('id')
                ->on('ticket')
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
        Schema::table('seat', function (Blueprint $table) {
            $table->dropForeign('seat_ticket_idfk_1');
            $table->dropForeign('seat_room_idfk_1');
        });
    }
}
