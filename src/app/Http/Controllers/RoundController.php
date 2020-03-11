<?php

namespace App\Http\Controllers;

use App\Round;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @return Round|array|JsonResponse
     */
    public function store(Request $request)
    {

        try {

            $newRound = Round::newRound();

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }

        return $this->show($newRound);

    }

    /**
     * Display the specified resource.
     *
     * @param Round $round
     * @return array
     */
    public function show(Round $round)
    {

        /** @var User $user */
        $me = auth()->user();

        /** @var Round $round */
        $round->load('host');
        $round->load('cardToFill');


        $players = $round->players();
        $players->each(function (User $player) {
            $player->setAttribute('picked_cards',
                $player->cardsInHand()->picked(true)->get());
        });
        $round->setAttribute('players', $players);

        $round->append('ready_to_pick');

        return [
            'me' => $me,
            'round' => $round,
            'my_cards' => $me->cardsInHand()->picked(false)->get(),
            'picked_cards' => $me->cardsInHand()->picked(true)->get(),
        ];

    }

    /**
     * @param Request $request
     * @param Round $round
     * @param User $winner
     * @return JsonResponse
     */
    public function close_round(Request $request, Round $round, User $winner)
    {

        if (!$round->opened) {
            return response()->json(['error' => 'Round non aperto'], 400);
        }

        $winner->cardsInHand()->picked()->update([
            'win_count' => DB::raw('`win_count` + 1 ')
        ]);

        $winner->score = $winner->score + 1;
        $winner->save();

        $round->opened = false;
        $round->save();

        return [];

    }

}
