<?php

namespace App\Http\Controllers;

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
        $round->setAttribute('players', $round->players());

        return [
            'me' => $me,
            'round' => $round,
            'my_cards' => $me->cardsInHand()->picked(false)->get(),
            'picked_cards' => $me->cardsInHand()->picked(true)->get(),
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
