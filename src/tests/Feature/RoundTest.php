<?php

namespace Tests\Feature;


use Tests\TestCase;

class RoundTest extends TestCase
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
     * @return void
     */
    public function testPlayPageNotLogged(): void
    {
        $response = $this->get('/home');
        $response->assertStatus(302);
    }

    /**
     * TODO test
     * @return void
     */
    public function playPageLogged(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }


}
