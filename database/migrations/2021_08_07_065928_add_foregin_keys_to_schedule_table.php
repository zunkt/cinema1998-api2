<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeginKeysToScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule', function (Blueprint $table) {
            $table->foreign('movie_id', 'schedule_movie_idfk_1')
                ->references('id')
                ->on('movie')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->foreign('room_id', 'schedule_room_idfk_1')
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
        Schema::table('schedule', function (Blueprint $table) {
            //
        });
    }
}
