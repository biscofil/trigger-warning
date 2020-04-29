<?php

namespace Tests\Feature\TriggerWarning;


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

        $this->assertCount(2, User::approved()->active()->get());

        Round::newRound();

    }


}
