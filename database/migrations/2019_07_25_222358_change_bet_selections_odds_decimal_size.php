<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBetSelectionsOddsDecimalSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bet_selections', function (Blueprint $table) {
            $table->decimal('odds', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bet_selections', function (Blueprint $table) {
            $table->decimal('odds', 5, 2)->change();
        });
    }
}
