<?php

namespace Tests\Feature\TriggerWarning;


use App\Card;
use App\Round;
use App\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Class RoundTest
 * @package Tests\Feature\TriggerWarning
 */
class RoundTest extends TestCase
{

    /**
     * @test
     */
    public function new_round()
    {

        Round::query()->delete();
        Card::query()->delete();

        config(['game.trigger_warning.cards_per_user' => 1]);
        config(['game.trigger_warning.min_users_for_round' => 2]);
        config(['game.trigger_warning.card_random.wait_hours' => 0]);

        Round::open()->update(['opened' => false]); // close all

        Cache::forget(User::CacheUserListKey);

        /** @var User $userA */
        $userA = factory(User::class)->create();
        $userA->setOnline(true);

        sleep(1);

        /** @var User $userB */
        $userB = factory(User::class)->create();
        $userB->setOnline(true);

        sleep(1);

        $this->assertTrue(in_array($userA->id, User::getCacheOnlineUserList()));
        $this->assertTrue(in_array($userB->id, User::getCacheOnlineUserList()));

        $active = User::approved()->active()->get()->pluck('id')->toArray();
        $this->assertTrue(in_array($userA->id, $active));
        $this->assertTrue(in_array($userB->id, $active));

        // normal cards
        factory(Card::class)->create();

        // card with placeholder
        /** @var Card $cardWithPlaceholder */
        $cardWithPlaceholder = factory(Card::class)->make();
        $cardWithPlaceholder->original_content = Card::NAME_PLACEHOLDER;
        $cardWithPlaceholder->save();

        /** @var Card $toFill */
        $toFill = factory(Card::class)->make();
        $toFill->type = Card::TypeCartToFill;
        $toFill->save();

        sleep(2);

        $round = Round::newRound();

        $this->assertStringContainsString(Card::NAME_PLACEHOLDER, $cardWithPlaceholder->original_content);
        
        $cardWithPlaceholder = $cardWithPlaceholder->fresh();
        $this->assertStringContainsString(Card::NAME_PLACEHOLDER, $cardWithPlaceholder->original_content);
        // TODO $this->assertStringNotContainsString(Card::NAME_PLACEHOLDER, $cardWithPlaceholder->content);

        // test close

        $closeResponse = $this->actingAs($userA)
            ->json('POST', 'api/games/trigger_warning/rounds/' . $round->id . '/close', [
                'winner_user_id' => null
            ]);
        $closeResponse->assertStatus(200);

    }


}
