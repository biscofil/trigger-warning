<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelegramIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('telegram_auth_code')->nullable()->unique();
            $table->string('telegram_id')->nullable()->unique();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique(['telegram_id']);
            $table->dropColumn('telegram_id');

            $table->dropUnique(['telegram_auth_code']);
            $table->dropColumn('telegram_auth_code');

        });
    }
}
