<?php

namespace App\Http\Controllers\OneWordEach;

use App\Http\Controllers\Controller;
use App\User;
use App\Word;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CardController
 * @package App\Http\Controllers
 */
class WordController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return array|JsonResponse|Response
     */
    public function store(Request $request)
    {

        /** @var User $user */
        $me = auth()->user();

        $validatedData = $request->validate([
            'word' => ['required', 'max:255'],
            'forbidden_words' => ['required', 'max:255'],
        ]);

        try {

            Word::create([
                'word' => strtolower($validatedData['word']),
                'forbidden_words' => strtolower($validatedData['forbidden_words']),
                'creator_user_id' => $me->id
            ]);

            return [];

        } catch (\Exception $exception) {

            return response()->json(['error' => 'La carta esiste già'], 400);

        }


    }

}
