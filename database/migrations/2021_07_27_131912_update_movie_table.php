<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMovieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movie', function (Blueprint $table) {
            $table->integer('slot');
            $table->text('descriptionContent')->nullable();
            $table->string('type')->nullable();
            $table->string('imageText')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movie', function (Blueprint $table) {
            $table->dropColumn('descriptionContent');
            $table->dropColumn('type');
            $table->dropColumn('slot');
            $table->dropColumn('imageText');
        });
    }
}
