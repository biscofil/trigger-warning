<?php

namespace App;

use App\Exceptions\GameException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class WordRound
 * @package App
 * @property mixed id
 * @property mixed word_id
 * @property Word word
 *
 * @property mixed guessing_user_id
 * @property User guessingUser
 *
 * @property mixed first_suggesting_user_id
 * @property User firstSuggestingUser
 *
 * @property mixed second_suggesting_user_id
 * @property User secondSuggestingUser
 *
 * @property bool opened
 *
 * @property null|Collection players
 * @method static self|Builder open()
 */
class WordRound extends Model
{

    protected $fillable = [
        'opened'
    ];

    protected $casts = [
        'opened' => 'bool'
    ];

    /**
     * @return Round|null
     */
    static function getOpenRound(): ?self
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
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    /**
     * @return BelongsTo
     */
    public function guessingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guessing_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function firstSuggestingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_suggesting_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function secondSuggestingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_suggesting_user_id');
    }

    /**
     * @return WordRound
     * @throws GameException
     * @throws Exception
     */
    public static function newRound(): self
    {

        Log::debug("#### NEW WORD ROUND ####");

        self::checkOpenRounds();


        self::checkNumberOfPlayers();

        $newRound = new self();
        $newRound->setGuessingUser();

        $newRound->checkNumberOfCards();
        $newRound->setSuggestingUsers();
        $newRound->setWord();
        $newRound->opened = true;
        $newRound->save();

        Log::debug("Created word round " . $newRound->id);

        return $newRound;
    }

    /**
     *
     * @throws GameException
     */
    private static function checkNumberOfPlayers(): void
    {
        if (User::approved()->active()->count() < 3) {
            throw new GameException('Servono almeno 3 dolcissimi.');
        }
    }

    /**
     *
     * @throws GameException
     */
    private function checkNumberOfCards(): void
    {
        if (Word::query()
                ->where('usage_count', '=', 0)
                ->where('creator_user_id', '<>', $this->guessing_user_id)
                ->count() == 0) {
            throw new GameException('Mancano parole...');
        }
    }

    /**
     * @throws GameException
     */
    private static function checkOpenRounds(): void
    {
        if (self::open()->count()) {
            throw new GameException('Ci sono round aperti!');
        }
    }

    /**
     *
     */
    private function setWord(): void
    {

        $word = Word::query()
            ->where('usage_count', '=', 0)
            ->where('creator_user_id', '<>', $this->guessing_user_id)
            ->inRandomOrder()
            ->first();

        $this->word_id = $word->id;
    }

    /**
     * @return void
     * @throws GameException
     */
    private function setGuessingUSer(): void
    {

        $host = null;

        $lastRound = self::query()->orderBy('id', 'desc')->first();
        if (is_null($lastRound)) {
            // first round ever

            /** @var User $host */
            $host = User::approved()->active()->first();

        } else {

            /** @var WordRound $lastRound */

            $host = User::approved()
                ->active()
                ->where('id', '>', $lastRound->guessing_user_id)
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

        $this->guessing_user_id = $host->id;

    }

    /**
     *
     */
    public function setSuggestingUsers(): void
    {

        $users = User::approved()
            ->active()
            ->where('id', '<>', $this->guessing_user_id)
            ->inRandomOrder()
            ->limit(2)
            ->get();

        $this->first_suggesting_user_id = $users->get(0)->id;
        $this->second_suggesting_user_id = $users->get(1)->id;

    }

    /**
     *
     */
    public function players(): Collection
    {
        return User::approved()
            ->active()
            ->get();
    }

    /**
     * @param bool $success
     * @return void
     * @throws GameException
     */
    public function close(bool $success): void
    {

        if (!$this->opened) {
            throw new GameException('Round non aperto');
        }

        $guessingInc = $success ? 2 : -2;
        $suggestingInc = $success ? 1 : -1;

        $this->guessingUser->score += $guessingInc;
        $this->guessingUser->save();

        $this->firstSuggestingUser->score += $suggestingInc;
        $this->firstSuggestingUser->save();
        $this->secondSuggestingUser->score += $suggestingInc;
        $this->secondSuggestingUser->save();

        $this->word->usage_count += 1;
        $this->word->save();

        $this->opened = false;
        $this->save();

    }

}
