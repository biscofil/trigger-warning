<?php

namespace App\Http\Controllers\TriggerWarning;

use App\Card;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewCardRequest;
use App\Round;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Class CardController
 * @package App\Http\Controllers
 */
class CardController extends Controller
{
    /**
     * @param Round $round
     * @param Card $card
     * @param Request $request
     * @return array|JsonResponse
     */
    public function setPicked(Round $round, Card $card, Request $request)
    {

        $openRound = Round::getOpenRound();

        if (is_null($openRound)) {
            return response()->json(['error' => 'Nessun round aperto'], 400);
        }

        if ($openRound->id !== $round->id) {
            return response()->json(['error' => 'Round non aperto'], 400);
        }

        /** @var User $user */
        $me = auth()->user();

        $otherPickedCardsCount = $me->cardsInHand()->picked()->where('id', '<>', $card->id)->count();

        if ($otherPickedCardsCount >= $round->cardToFill->spaces_count) {
            return response()->json(['error' => 'Carte da scegliere esaurite'], 400);
        }

        if ($request->has('to_pos')) {

            if ($request->has('from_pos')) {

                // reorder

                $me->cardsInHand()
                    ->picked()
                    ->where('id', '<>', $card->id)
                    ->where('order', '=', $request->get('to_pos'))
                    ->update([
                        'order' => $request->get('from_pos')
                    ]);

            } else {

                // new
                $me->cardsInHand()
                    ->picked()
                    ->where('id', '<>', $card->id)
                    ->where('order', '=', $request->get('to_pos'))
                    ->update([
                        'order' => DB::raw('`order` + 1')
                    ]);

            }

            $card->order = $request->get('to_pos');

        }

        $card->picked = true;
        $card->save();

        return [
            'picked_cards' => $me->cardsInHand()->picked(true)->get()
        ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewCardRequest $request
     * @return array|JsonResponse|Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(NewCardRequest $request)
    {

        /** @var User $user */
        $me = auth()->user();

        $request->store($me);

        return [];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Card $card
     * @return Response
     */
    public function update(Request $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Card $card
     * @return Response
     */
    public function destroy(Card $card)
    {
        //
    }
}
