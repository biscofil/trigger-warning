<?php

namespace App;

use App\Exceptions\GameException;
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
    public function players(): Collection
    {
        return User::approved()
            ->active()
            ->where('id', '<>', $this->host_user_id)
            ->get();
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

    // ############################ NEW ROUND #################################

    /**
     * @return Round
     * @throws GameException
     * @throws Exception
     */
    public static function newRound(): Round
    {

        Log::debug("#### NEW ROUND ####");

        self::checkOpenRounds();

        self::clearPlayersPickedCards();

        self::clearPlayersDeck();

        self::checkNumberOfPlayers();

        $newRound = new Round();

        $newRound->getNewHost();

        DB::beginTransaction();

        try {

            $newRound->getMainCardToBeFilled();

            // Assign cards
            $newRound->giveCardsToPlayers();

            $newRound->opened = true;
            $newRound->save();

            DB::commit();

            Log::debug("Created round " . $newRound->id);

        } catch (Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage());

            throw $exception;

        }

        return $newRound;
    }

    /**
     * @throws GameException
     */
    private static function checkNumberOfPlayers(): void
    {
        $minUsersForRound = config('game.min_users_for_round');
        if (User::approved()->active()->count() < $minUsersForRound) {
            throw new GameException('Servono almeno ' . $minUsersForRound . ' stronzi');
        }

    }

    /**
     * @return void
     * @throws GameException
     */
    private function getNewHost(): void
    {

        $host = null;

        $lastRound = Round::query()->orderBy('id', 'desc')->first();
        if (is_null($lastRound)) {
            // first round ever

            /** @var User $host */
            $host = User::approved()->active()->first();

        } else {

            /** @var Round $lastRound */

            $host = User::approved()
                ->active()
                ->where('id', '>', $lastRound->host_user_id)
                ->orderBy('id', 'asc')
                ->first();

            if (is_null($host)) {

                //last registered user, start from the first ID

                $host = User::approved()
                    ->active()
                    ->orderBy('id', 'asc')
                    ->first();

            }

        }

        if (is_null($host)) {
            throw new GameException("Errore durante l'elezione del nuovo host");
        }

        $this->host_user_id = $host->id;

    }

    /**
     * @throws GameException
     */
    private function getMainCardToBeFilled(): void
    {
        $mainCard = Card::toFill()
            ->orderBy('usage_count', 'asc')
            ->orderBy('updated_at', 'asc')
            //->inRandomOrder()
            ->first();

        if (is_null($mainCard)) {
            throw new GameException('Serve almeno una carta da riempire');
        }

        /** @var Card $mainCard */
        $this->main_card_id = $mainCard->id;

        $mainCard->usage_count = $mainCard->usage_count + 1;
        $mainCard->save();

    }

    /**
     * @throws GameException
     */
    private static function checkOpenRounds(): void
    {
        if (Round::open()->count()) {
            throw new GameException('Ci sono round aperti!');
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
     *
     */
    private static function clearPlayersDeck(): void
    {

        if (config('game.reset_cards_every_round')) {

            Log::debug("Clearing all the user decks");

            Card::query()
                ->whereNotNull('user_id') // in someone's deck
                ->update(['user_id' => null]); // reset

        }

    }

    /**
     * @throws GameException
     */
    private function giveCardsToPlayers(): void
    {

        Log::debug("giveCardsToPlayers");

        $players = $this->players();

        $requiredCardCount = $this->getRequiredCards($players);

        $cards = Card::filling()->inMainDeck();

        // TODO give priority to older or not used

        // check if we have enough cards
        if ($cards->count() < $requiredCardCount) {
            throw new GameException('Non ci sono abbastanza carte, ne servono almeno '
                . $requiredCardCount . ' ma ce ne sono solo ' . $cards->count());
        }

        $cardsToAssign = $cards
            ->limit($requiredCardCount)
            ->orderBy('usage_count', 'asc')
            ->orderBy('updated_at', 'asc')
            ->get()
            ->shuffle();

        Log::debug("fetched " . $cardsToAssign->count() . " cards");

        $this->assignCards($cardsToAssign, $players);

    }

    /**
     * @param Collection $players
     * @return int
     */
    private function getRequiredCards(Collection $players): int
    {

        // only given missing card

        // get the number of needed cards for another round
        $requiredCardCount = $players->sum(function (User $player) {
            return $player->cardsNeeded();
        });

        // TODO return collection with userid => needed

        Log::debug("We need " . $requiredCardCount . " cards");

        return $requiredCardCount;
    }

    /**
     * @param Collection $cardsToAssign
     * @param Collection $players
     */
    private function assignCards(Collection $cardsToAssign, Collection $players): void
    {

        foreach ($players as $player) {

            /** @var User $player */

            $cardsNeeded = $player->cardsNeeded();

            for ($a = 0; $a < $cardsNeeded; $a++) {

                Log::debug("adding card to user " . $player->id);

                /** @var Card $card */
                $card = $cardsToAssign->pop();

                // TODO fix : Call to a member function owner() on null
                $card->owner()->associate($player);
                $card->usage_count = $card->usage_count + 1;
                $card->save();

            }

        }
    }

    // ############################# CLOSE ROUND ################################

    /**
     * @param User $winner
     * @return void
     * @throws GameException
     */
    public function close(User $winner): void
    {

        if (!$this->opened) {
            throw new GameException('Round non aperto');
        }

        $winner->cardsInHand()->picked()->update([
            'win_count' => DB::raw('`win_count` + 1 ')
        ]);

        $winner->score = $winner->score + 1;
        $winner->save();

        $this->opened = false;
        $this->save();

    }


    // #############################################################

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
