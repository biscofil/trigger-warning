<?php


namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{

    /**
     * @return string
     */
    public function index()
    {
        return view('index');
    }

    /**
     *
     */
    public function xhr_play()
    {

        $me = $this->getAuthUser();

        return [
            'me' => $me,
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

