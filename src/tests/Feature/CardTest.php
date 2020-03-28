<?php

namespace Tests\Feature;

use App\Card;
use Tests\TestCase;

class CardTest extends TestCase
{

    /**
     * @return void
     */
    public function testScope(): void
    {

        try{

            Card::smartRandom()->get();

            $this->assertTrue(true);

        }catch (\Exception $ex){

            $this->assertTrue(false);

        }

    }



}
