<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateWordRoundsTable
 */
class CreateWordRoundsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('word_rounds', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->boolean('opened')->default(false);

            $table->unsignedBigInteger('word_id');
            $table->foreign('word_id')->references('id')->on('words');

            $table->unsignedBigInteger('guessing_user_id');
            $table->foreign('guessing_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('first_suggesting_user_id');
            $table->foreign('first_suggesting_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('second_suggesting_user_id');
            $table->foreign('second_suggesting_user_id')->references('id')->on('users');

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
        Schema::dropIfExists('word_rounds');
    }
}
