<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Card
 * @package App
 * @property mixed id
 * @property mixed type
 * @property mixed content
 * @property bool approved
 * @property int usage_count
 * @property int win_count
 * @property int spaces_count
 * @property null|int user_id
 * @property null|int creator_user_id
 * @property bool picked
 * @property int order
 * @method static self|Builder toFill()
 * @method static self|Builder inMainDeck()
 * @method static self|Builder filling()
 * @method static self|Builder picked(bool $true)
 */
class Card extends Model
{

    public static $CardsPerUser = 1; //11;

    public static $TypeCartToFill = 1;
    public static $TypeFillingCart = 2;

    protected $fillable = [
        'id',
        'type',
        'content',
        'picked',
        'order', // used for deck and picked cards
        'user_id',
        'usage_count',
        'win_count',
        'creator_user_id',
    ];

    protected $appends = [
        'spaces_count'
    ];

    protected $casts = [
        'approved' => 'bool',
        'picked' => 'bool'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /*static::addGlobalScope('approved', function (Builder $builder) {
            $builder->where('approved', '=', true);
        });*/

    }

    /**
     * @param Builder $query
     * @param bool $approved
     * @return Builder
     */
    public function scopeApproved(Builder $query, bool $approved = true): Builder
    {
        return $query->where('approved', '=', $approved);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeToFill(Builder $query): Builder
    {
        return $query->where('type', '=', self::$TypeCartToFill);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFilling(Builder $query): Builder
    {
        return $query->where('type', '=', self::$TypeFillingCart);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeInMainDeck(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    /**
     * @param Builder $query
     * @param bool $picked
     * @return Builder
     */
    public function scopePicked(Builder $query, bool $picked = true): Builder
    {
        return $query->where('picked', '=', $picked);
    }

    /**
     * Return the user who created this card
     * @return BelongsTo
     */
    public function creator()
    {
        return self::belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Returns the user that has this card in hand
     * @return BelongsTo
     */
    public function owner()
    {
        return self::belongsTo(User::class, 'user_id');
    }

    /**
     * Returns the number of spaces to be filled
     * @return int
     */
    public function getSpacesCountAttribute(): int
    {
        return substr_count($this->content, '@');
    }

}
