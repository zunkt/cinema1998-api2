<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTheaterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theater', function (Blueprint $table) {
            $table->foreign('movie_id', 'theater_movie_idfk_1')
                ->references('id')
                ->on('movie')
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
        Schema::table('theater', function (Blueprint $table) {
            $table->dropForeign('theater_movie_idfk_1');
        });
    }
}
