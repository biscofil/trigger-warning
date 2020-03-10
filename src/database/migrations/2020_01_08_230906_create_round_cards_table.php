<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRoundCardsTable
 */
class CreateRoundCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round_cards', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedInteger('order');

            $table->unsignedBigInteger('round_id');
            $table->foreign('round_id')->references('id')->on('rounds');

            $table->unsignedBigInteger('card_id');
            $table->foreign('card_id')->references('id')->on('cards');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('round_cards');
    }
}
