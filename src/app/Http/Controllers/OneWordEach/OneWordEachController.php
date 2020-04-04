<?php


namespace App\Http\Controllers\OneWordEach;


use App\Http\Controllers\Controller;
use App\User;
use App\Word;
use App\WordRound;

/**
 * Class OneWordEachController
 * @package App\Http\Controllers\OneWordEac
 */
class OneWordEachController extends Controller
{

    /**
     * @return string
     */
    public function index()
    {
        $this->getAuthUser();
        return view('one_word_each.one_word_each');
    }

    /**
     *
     */
    public function xhr_play()
    {

        $me = $this->getAuthUser();

        $round = WordRound::getOpenRound();

        return [
            'me' => $me,
            'round_id' => $round ? $round->id : null,
            'users' => User::approved()->where('id', '<>', $me->id)->get(),
            'words' => Word::count(),
        ];

    }


}
