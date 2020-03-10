<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property bool approved
 *
 * @property mixed provider
 * @property mixed provider_id
 *
 * @property mixed avatar
 * @property mixed avatar_original
 *
 * @package App
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
        'provider', 'provider_id',
    ];

    public function cardsInHand(){

        return $this->hasMany(Card::class );

    }


}
