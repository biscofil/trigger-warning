<?php


namespace Tests\Feature;

use App\User;
use Tests\TestCase;

/**
 * Class AuthTest
 * @package Tests\Feature
 */
class AuthTest extends TestCase
{

    /**
     * @return void
     */
    public function testHomepage(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function testPlayPageNotLogged(): void
    {
        $response = $this->get('/games/trigger_warning');
        $response->assertStatus(302);
        $response = $this->get('/games/one_word_each');
        $response->assertStatus(302);
    }

    /**
     * @test
     * @return void
     */
    public function playPageLogged(): void
    {

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get('/games/trigger_warning');
        $response->assertStatus(200);
        $response = $this->get('/games/one_word_each');
        $response->assertStatus(200);
    }

}
