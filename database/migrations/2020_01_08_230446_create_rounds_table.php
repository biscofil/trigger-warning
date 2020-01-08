<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRoundsTable
 */
class CreateRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->boolean('opened')->default(false);

            $table->unsignedBigInteger('host_user_id');
            $table->foreign('host_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('main_card_id');
            $table->foreign('main_card_id')->references('id')->on('cards');

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
        Schema::dropIfExists('rounds');
    }
}
