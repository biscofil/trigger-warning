<?php

namespace App\Http\Controllers\OneWordEach;

use App\Exceptions\GameException;
use App\Http\Controllers\Controller;
use App\User;
use App\WordRound;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class RoundController
 * @package App\Http\Controllers
 */
class RoundController extends Controller
{

    /**
     * new round
     *
     * @param Request $request
     * @return WordRound|array|JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {

        try {

            $newRound = WordRound::newRound();

        } catch (GameException $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }

        return $this->show($newRound);

    }

    /**
     * Display the specified resource.
     *
     * @param WordRound $round
     * @return array
     */
    public function show(WordRound $round)
    {

        /** @var User $user */
        $me = auth()->user();

        /** @var WordRound $round */
        $round->load('word');
        $round->load('guessingUser');
        $round->load('firstSuggestingUser');
        $round->load('secondSuggestingUser');

        /*$players = $round->players();
        $players->each(function (User $player) {
            $player->setAttribute('picked_cards',
                $player->cardsInHand()->picked(true)->get());
        });
        $round->setAttribute('players', $players);

        $round->append('ready_to_pick');*/

        return [
            'me' => $me,
            'round' => $round,
        ];

    }

    /**
     * @param Request $request
     * @param WordRound $round
     * @return array|JsonResponse
     */
    public function close_round(Request $request, WordRound $round)
    {

        $validatedData = $request->validate([
            'success' => ['required', 'boolean'],
        ]);

        try {

            $round->close(boolval($validatedData['success']));

        } catch (GameException $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }

        return [];

    }

}
