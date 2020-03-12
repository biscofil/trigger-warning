<?php


namespace App\Http\Controllers;

use App\Round;
use App\User;
use Illuminate\Http\JsonResponse;

/**
 * Class GameController
 * @package App\Http\Controllers
 */
class GameController extends Controller
{

    private function getAuthUser(): User
    {
        /** @var User $user */
        $me = auth()->user();

        $me->active = true;
        $me->save();

        return $me;
    }

    /**
     * @return string
     */
    public function play()
    {
        $this->getAuthUser();
        return view('play');
    }

    /**
     */
    public function xhr_play()
    {

        $me = $this->getAuthUser();

        $round = Round::getOpenRound();

        return [
            'round_id' => $round ? $round->id : null,
            'users' => User::approved()->where('id', '<>', $me->id)->get()
        ];

    }

    /**
     * @param User $user
     * @param bool $active
     * @return array|JsonResponse
     */
    public function setUserActive(User $user, bool $active)
    {

        $me = $this->getAuthUser();

        if ($me->id === $user->id) {
            return response()->json(['error' => 'Non puoi modificare il tuo stato!'], 400);
        }

        $user->active = $active;
        $user->save();

        return [
            'users' => User::approved()->where('id', '<>', $me->id)->get()
        ];

    }

}

