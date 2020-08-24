<?php


namespace App\Http\Controllers\TriggerWarning;

use App\Card;
use App\Http\Controllers\Controller;
use App\Round;
use App\TriggerWarningTelegramBot;
use App\User;

/**
 * Class TriggerWarningController
 * @package App\Http\Controllers
 */
class TriggerWarningController extends Controller
{

    /**
     * @return string
     */
    public function index()
    {
        $this->getAuthUser();
        return view('trigger_warning.trigger_warning');
    }

    /**
     *
     */
    public function xhr_play()
    {

        $me = $this->getAuthUser();

        $round = Round::getOpenRound();

        return [
            'me' => $me,
            'round_id' => $round ? $round->id : null,
            'users' => User::approved()->where('id', '<>', $me->id)->get(),
            'cards' => [
                'to_fill' => Card::toFill()->count(),
                'filling' => Card::filling()->count()
            ]
        ];

    }

    /**
     * @return array
     */
    public function telegram_webhook()
    {

        $k = new TriggerWarningTelegramBot();
        $k->parse_webhook(request()->toArray());
        return [];

    }

}

