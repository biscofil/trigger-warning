<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
 * @method static Builder approved()
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
        'score',
        'provider', 'provider_id',
        'avatar', 'avatar_original'
    ];

    protected $casts = [
        'approved' => 'bool'
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
     * @return HasMany
     */
    public function cardsInHand(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * @return int
     */
    public function cardsNeeded() : int{
        return Card::$CardsPerUser - $this->cardsInHand()->count();
    }

}
