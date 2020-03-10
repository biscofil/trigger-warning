<?php


namespace App\Http\Controllers;

use App\Round;

/**
 * Class GameController
 * @package App\Http\Controllers
 */
class GameController extends Controller
{

    /**
     * @return string
     */
    public function play()
    {
        return view('play');
    }

    /**
     */
    public function xhr_play()
    {

        // TODO if open round
        // if use in it
        // else wait

        $round = Round::orderBy('id', 'desc')->first();

        return [
            'round_id' => $round ? $round->id : null
        ];
    }

}

