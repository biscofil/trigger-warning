<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Round
 * @package App
 * @property mixed id
 * @property int host_user_id
 * @property int main_card_id
 * @property boolean opened
 * @property Card cardToFill
 * @property null|Collection players
 * @method static self|Builder first()
 * @method static self|Builder open()
 */
class Round extends Model
{

    public static $UserPerRound = 2; //3;

    protected $fillable = [
        'host_user_id',
        'main_card_id',
        'opened'
    ];

    protected $casts = [
        'opened' => 'bool'
    ];

    /**
     * @return Round|null
     */
    static function getOpenRound(): ?Round
    {
        return self::open()
            ->orderBy('id', 'desc')
            ->limit(1)
            ->first();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('opened', '=', true);
    }


    /**
     * @return BelongsTo
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    /**
     */
    public function players()
    {
        return User::approved()->where('id', '<>', $this->host_user_id)->get();
    }

    /**
     * @return BelongsTo
     */
    public function cardToFill(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'main_card_id');
    }

    /**
     * @return HasMany
     */
    public function submittedCards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * @return Round
     * @throws Exception
     */
    public static function newRound(): Round
    {

        self::checkOpenRounds();

        self::clearPlayersPickedCards();

        $newRound = new Round();

        $users = User::approved();
        if ($users->count() < Round::$UserPerRound) {
            throw new Exception('Servono almeno ' . Round::$UserPerRound . ' stronzi');
        }
        /** @var User $host */
        $host = $users->first();
        $newRound->host_user_id = $host->id;


        $mainCard = Card::toFill()->inRandomOrder()->first();
        if (is_null($mainCard)) {
            throw new Exception('Serve almeno una carta da riempire');
        }
        /** @var Card $mainCard */
        $newRound->main_card_id = $mainCard->id;

        DB::beginTransaction();

        try {

            // Assign cards
            $newRound->giveCardsToPlayers();

            $newRound->opened = true;
            $newRound->save();

            DB::commit();

        } catch (Exception $exception) {

            DB::rollBack();

            throw  $exception;

        }

        return $newRound;
    }

    /**
     * @throws Exception
     */
    private static function checkOpenRounds(): void
    {
        if (Round::open()->count()) {
            throw new Exception('Ci sono round aperti!');
        }
    }

    /**
     *
     */
    private static function clearPlayersPickedCards(): void
    {
        Card::picked(true)->update([
            'user_id' => null,
            'picked' => false
        ]);
    }

    /**
     * @throws Exception
     */
    private function giveCardsToPlayers(): void
    {

        Log::debug("giveCardsToPlayers");

        $players = $this->players();

        // get the number of needed cards for another round
        $requiredCardCount = $players->sum(function (User $player) {
            return $player->cardsNeeded();
        });

        Log::debug("We need  " . $requiredCardCount . " cards");

        $cards = Card::filling()->inMainDeck();

        // check if we have enough cards
        if ($cards->count() < $requiredCardCount) {
            throw new Exception('Non ci sono abbastanza carte, ne servono almeno '
                . $requiredCardCount . ' ma ce ne sono solo ' . $cards->count());
        }

        $cardsToAssign = $cards->limit($requiredCardCount)->get()->shuffle();

        Log::debug("fetched " . $cardsToAssign->count() . " cards");

        foreach ($players as $player) {

            /** @var User $player */

            for ($a = 0; $a < $player->cardsNeeded(); $a++) {

                /** @var Card $card */
                $card = $cardsToAssign->pop();

                $card->owner()->associate($player)->save();

            }

        }

    }

    /**
     * @return bool
     */
    public function getReadyToPickAttribute(): bool
    {
        foreach ($this->getAttribute('players') as $player) {

            /** @var User $player */
            if (!$player->getReadyAttribute()) {
                return false;
            }

        }

        return true;
    }

}
