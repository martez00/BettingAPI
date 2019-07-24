<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_selections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bet_id');
            $table->unsignedBigInteger('selection_id');
            $table->decimal('odds', 5, 3);

            $table->foreign('bet_id')
                ->references('id')
                ->on('bets')
                ->onDelete('cascade');

            $table->foreign('selection_id')
                ->references('id')
                ->on('selections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bet_selections');
    }
}
