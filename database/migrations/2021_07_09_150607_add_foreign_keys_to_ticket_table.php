<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket', function (Blueprint $table) {
            $table->foreign('schedule_id', 'ticket_schedule_idfk_1')
                ->references('id')
                ->on('schedule')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
            $table->foreign('user_id', 'ticket_user_idfk_1')
                ->references('id')
                ->on('user')
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
        Schema::table('ticket', function (Blueprint $table) {
            $table->dropForeign('ticket_schedule_idfk_1');
            $table->dropForeign('ticket_user_idfk_1');
        });
    }
}
