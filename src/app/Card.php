<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Class Card
 * @package App
 * @property int|null id
 * @property int type
 * @property string content
 * @property string|null original_content
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
 * @method static self|Builder smartRandom()
 * @method static self|Builder notRecentlyCreated()
 * @method static self|Builder notUsedTooManyTimes()
 * @method static self|Builder picked(bool $true)
 * @method static self create(array $array)
 * @method static self|null find($id)
 * @method static self findOrFail($id)
 */
class Card extends Model
{
    public const TypeCartToFill = 1;
    public const TypeFillingCart = 2;

    public const NAME_PLACEHOLDER = '{{name}}';

    protected $fillable = [
        'id',
        'type',
        'content',
        'original_content',
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
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotRecentlyCreated(Builder $query): Builder
    {

        $waitHours = config('game.trigger_warning.card_random.wait_hours');

        $query->where('created_at', '<',
            Carbon::now()->subHours($waitHours)->toDateTimeString());

        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotUsedTooManyTimes(Builder $query): Builder
    {

        $usageLimit = config('game.trigger_warning.card_random.usage_count_limit');

        if (!is_null($usageLimit)) {
            $query->where('usage_count', '<', intval($usageLimit));
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSmartRandom(Builder $query): Builder
    {

        $orderByParts = [];

        // ########## USAGE COUNT

        // usage count is 1 if the card is the most used one
        // usage count is 0 if the card is the least used one
        $usageCountMultiplier = config('game.trigger_warning.card_random.usage_count_multiplier');
        if (!is_null($usageCountMultiplier)) {
            $usageCountMultiplier = floatval($usageCountMultiplier);
            $usageCountNormalized = "usage_count / ( SELECT MAX(usage_count) + 1 FROM cards )";
            $orderByParts[] = "( ($usageCountNormalized) * $usageCountMultiplier )";
        }

        // ########## WIN COUNT

        // win count is 1 if the card is the most winning one
        // win count is 0 if the card is the least winning one
        $winCountMultiplier = config('game.trigger_warning.card_random.win_count_multiplier');
        if (!is_null($winCountMultiplier)) {
            $winCountMultiplier = floatval($winCountMultiplier);
            $winCountNormalized = "win_count / ( SELECT MAX(win_count) + 1 FROM cards )";
            $orderByParts[] = " ( ($winCountNormalized) * $winCountMultiplier )";
        }

        // ########## DAYS SINCE UPDATED_AT

        $daysMultiplier = config('game.trigger_warning.card_random.days_multiplier');
        if (!is_null($daysMultiplier)) {
            $daysMultiplier = floatval($daysMultiplier);
            $daysSinceLastUpdate = "DATEDIFF( NOW() , updated_at )";
            $daysSinceLastUpdateNormalized = "$daysSinceLastUpdate / ( SELECT MAX($daysSinceLastUpdate) + 1  FROM cards)";
            $orderByParts[] = "( ($daysSinceLastUpdateNormalized)  * $daysMultiplier )";
        }

        // ########## RANDOM

        $randomMultiplier = config('game.trigger_warning.card_random.random_multiplier');
        if (!is_null($randomMultiplier)) {
            $randomMultiplier = floatval($randomMultiplier);
            $orderByParts[] = "( RAND() * $randomMultiplier )";
        }

        // ####################### Build Query

        if (count($orderByParts)) {
            $query->orderByRaw(implode("+", $orderByParts) . " ASC");
        }

        return $query;

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
        return $query->where('type', '=', self::TypeCartToFill);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFilling(Builder $query): Builder
    {
        return $query->where('type', '=', self::TypeFillingCart);
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

    /**
     *
     */
    public function replacePlaceholders(): void
    {
        if (!is_null($this->original_content)) {
            $this->content = $this->original_content;

            // User name placeholder
            if (Str::contains($this->content, self::NAME_PLACEHOLDER)) {
                $count = substr_count($this->content, self::NAME_PLACEHOLDER);
                $users = User::query()->select('name')->get()->pluck('name')->toArray();

                $users = count($users) ? $users : ["qualcuno", "gesù"]; // fallback

                for ($i = 0; $i < $count; $i++) {
                    $this->content = str_replace(self::NAME_PLACEHOLDER, $users[array_rand($users)], $this->original_content);
                }
            }

        }
    }


}
