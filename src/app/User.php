<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class User
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property bool approved
 * @property int score
 *
 * @property mixed provider
 * @property mixed provider_id
 *
 * @property mixed avatar
 * @property mixed avatar_original
 *
 * @package App
 * @method static Builder|self approved()
 * @method static Builder|self active()
 */
class User extends Authenticatable
{

    public const CacheUserListKey = 'user_list';
    public const CacheOnlineUserList = 'online_users';

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'approved',
        'score',
        'provider', 'provider_id',
        'avatar', 'avatar_original'
    ];

    protected $casts = [
        'approved' => 'bool',
    ];

    protected $appends = [
        'ready', // computed for cards against humanity
        'online'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'provider', 'provider_id', 'email', 'approved'
    ];

    /**
     * SELECT creator_user_id, COUNT(id), (SUM(win_count) / SUM(usage_count)) * 100 as ratio
     * FROM cards
     * WHERE usage_count > 0
     * GROUP BY creator_user_id
     * ORDER BY ratio DESC
     */

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', '=', true);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('id', self::getCacheOnlineUserList());
    }

    /**
     *
     */
    public function generateCardsWithUserName(): void
    {

        $n = $this->name;

        $cardsToFill = [
            "A $n piace @",
            "$n non può fare a meno di @",
            "L'hobby preferito di $n? Sicuramente @",
            "Che merda! $n... Non puoi @ davanti a tutti",
        ];

        foreach ($cardsToFill as $cardToFill) {

            $c = new Card();
            $c->type = Card::$TypeCartToFill;
            $c->content = $cardToFill;
            $c->save();

        }

        $fillingCards = [
            "$n", // just the name
            "schiaffeggiare $n",
        ];

        foreach ($fillingCards as $fillingCard) {

            $c = new Card();
            $c->type = Card::$TypeFillingCart;
            $c->content = $fillingCard;
            $c->save();

        }

    }

    /**
     * @return HasMany
     */
    public function cardsInHand(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * @return int
     */
    public function cardsNeeded(): int
    {
        $cardsInHand = $this->cardsInHand()->count();

        $cardsPerUser = config('game.trigger_warning.cards_per_user');

        Log::debug("User " . $this->id .
            " has " . $cardsInHand . "/" . $cardsPerUser . " cards in hand");

        $count = $cardsPerUser - $cardsInHand;
        return $count > 0 ? $count : 0;
    }

    /**
     * @return bool
     */
    public function getReadyAttribute(): bool
    {
        $round = Round::getOpenRound();
        if ($round) {
            return $this->cardsInHand()->picked()->count() == $round->cardToFill->spaces_count;
        }
        return false;
    }

    /**
     * @param int $userID
     * @return string
     */
    private static function getOnlineCacheKey(int $userID): string
    {
        return 'user-is-online-' . $userID;
    }

    /**
     * @return void
     */
    public function setOnline(): void
    {
        Cache::put(
            self::getOnlineCacheKey($this->id),
            true,
            20        // keep it for 20 seconds
        );
        self::updateCacheUserList();
    }

    /**
     * @return bool
     */
    public function getOnlineAttribute(): bool
    {
        return Cache::has(self::getOnlineCacheKey($this->id));
    }

    /**
     * @param bool $force
     */
    public static function updateCacheUserList(bool $force = false): void
    {
        if ((!Cache::has(self::CacheUserListKey)) || $force) {
            Cache::forever(self::CacheUserListKey,
                User::query()
                    ->select('id')
                    ->pluck('id')
                    ->toArray(),
                );
        }
    }

    /**
     * @return array
     */
    public static function getCacheUserList(): array
    {
        self::updateCacheUserList();
        return Cache::get(self::CacheUserListKey);
    }

    /**
     * @return array
     */
    public static function getCacheOnlineUserList(): array
    {

        if (Cache::has(self::CacheOnlineUserList)) {

            // retrieve it
            return Cache::get(self::CacheOnlineUserList);

        } else {

            // compute it
            $out = [];
            foreach (self::getCacheUserList() as $userId) {
                if (Cache::has(self::getOnlineCacheKey($userId))) {
                    $out[] = $userId;
                }
            }
            Cache::put(self::CacheOnlineUserList, $out, 15); // keep it for 10 seconds
            return $out;

        }

    }

}
