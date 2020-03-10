<?php

namespace App\Http\Controllers;

use App\Card;
use App\Round;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @return Round|JsonResponse
     */
    public function store(Request $request)
    {

        $users = User::approved();

        if ($users->count() < 2) { // TODO 3
            return response()->json(['error' => 'Servono almeno 3 stronzi'], 400);
        }

        $cardsToFill = Card::toFill()->inRandomOrder();

        if ($cardsToFill->count() == 0) {
            return response()->json(['error' => 'Serve almeno una carta'], 400);
        }


        $newRound = new Round();

        /** @var User $host */
        $host = $users->first();
        $newRound->host_user_id = $host->id;

        /** @var Card $mainCard */
        $mainCard = $cardsToFill->first();
        $newRound->main_card_id = $mainCard->id;

        $newRound->save();

        return $newRound;

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
        $round->setAttribute('players', $round->players());

        return [
            'round' => $round,
            'my_cards' => $me->cardsInHand
        ];

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Round $round
     * @return Response
     */
    public function edit(Round $round)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Round $round
     * @return Response
     */
    public function update(Request $request, Round $round)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Round $round
     * @return Response
     */
    public function destroy(Round $round)
    {
        //
    }
}
