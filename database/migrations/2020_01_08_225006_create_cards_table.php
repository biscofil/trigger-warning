<?php

use App\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCardsTable
 */
class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedSmallInteger('type')->default(Card::TypeFillingCart);

            $table->boolean('approved')->default(false);

            $table->boolean('picked')->default(false);

            $table->string('content');

            $table->unsignedBigInteger('usage_count')->default(0);

            $table->unsignedBigInteger('win_count')->default(0);

            // user that has the card in hand
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            // user that has created the card
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->foreign('creator_user_id')->references('id')->on('users');

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
        Schema::dropIfExists('cards');
    }
}
