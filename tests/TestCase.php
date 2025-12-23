<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;
    use DatabaseTransactions;

    /**
     * @param bool $setOnline
     * @return User
     */
    protected function getUser(bool $setOnline = false): User
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        if ($setOnline) {
            $user->setOnline(true);
        }
        return $user;
    }

}
