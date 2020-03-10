<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Round
 * @package App
 * @property int host_user_id
 * @property int main_card_id
 * @method static first()
 */
class Round extends Model
{

    protected $fillable = [
        'host_user_id',
        'main_card_id',
    ];

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

}
