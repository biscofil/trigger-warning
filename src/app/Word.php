<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Word
 * @package App
 * @property mixed id
 * @property string word
 * @property string forbidden_words
 * @property int usage_count
 */
class Word extends Model
{

    protected $fillable = [
        'id',
        'word',
        'forbidden_words',
        'usage_count',
        'creator_user_id'
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
