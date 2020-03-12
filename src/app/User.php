<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

/**
 * Class User
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property bool approved
 * @property bool active
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

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'approved',
        'active',
        'score',
        'provider', 'provider_id',
        'avatar', 'avatar_original'
    ];

    protected $casts = [
        'approved' => 'bool',
        'active' => 'bool'
    ];

    protected $appends = [
        'ready'
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
        return $query->where('active', '=', true);
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

        $cardsPerUser = config('game.cards_per_user');

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


}
