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

        factory(Card::class)->create();
        factory(Card::class)->create();

        /** @var Card $toFill */
        $toFill = factory(Card::class)->create();
        $toFill->type = Card::TypeCartToFill;
        $toFill->save();

        Round::newRound();

    }


}
