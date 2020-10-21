<?php

use App\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOriginalContentToCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('original_content')->after('content')->nullable()->default(null);
        });

        $this->generateCards();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM cards WHERE original_content IS NOT NULL;");

        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('original_content');
        });
    }


    /**
     *
     */
    public function generateCards(): void
    {

        $cardsToFill = [
            "A " . Card::NAME_PLACEHOLDER . " piace @",
            Card::NAME_PLACEHOLDER . " non può fare a meno di @",
            "L'hobby preferito di " . Card::NAME_PLACEHOLDER . "? Sicuramente @",
            "Che merda! " . Card::NAME_PLACEHOLDER . "... Non puoi @ davanti a tutti",
        ];

        foreach ($cardsToFill as $cardToFill) {

            $c = new Card();
            $c->type = Card::TypeCartToFill;
            $c->content = "";
            $c->original_content = $cardToFill;
            $c->save();

        }

        $fillingCards = [
            Card::NAME_PLACEHOLDER, // just the name
            "schiaffeggiare " . Card::NAME_PLACEHOLDER,
        ];

        foreach ($fillingCards as $fillingCard) {

            $c = new Card();
            $c->type = Card::TypeFillingCart;
            $c->content = "";
            $c->original_content = $fillingCard;
            $c->save();

        }

    }


}
