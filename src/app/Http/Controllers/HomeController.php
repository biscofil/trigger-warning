<?php


namespace App\Http\Controllers;

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
     * @return string
     */
    public function play()
    {
        return view('play');
    }

}

