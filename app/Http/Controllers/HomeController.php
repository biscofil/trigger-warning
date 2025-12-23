<?php


namespace App\Http\Controllers;

use App\User;

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
            'users' => User::approved()->active()->where('id', '<>', $me->id)->get()
        ];

    }

    /**
     *
     */
    public function heartBeat()
    {
        // do nothing, LastUserActivity handles online status
    }

}

