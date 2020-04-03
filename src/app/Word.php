<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Word
 * @package App
 * @property mixed id
 * @property string word
 * @property int usage_count
 */
class Word extends Model
{

    protected $fillable = [
        'id',
        'word',
        'usage_count'
    ];

    /**
     * Return the user who created this card
     * @return BelongsTo
     */
    public function creator()
    {
        return self::belongsTo(User::class, 'creator_user_id');
    }

}
