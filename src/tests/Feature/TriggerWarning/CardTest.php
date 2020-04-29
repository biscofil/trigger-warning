<?php

namespace Tests\Feature\TriggerWarning;

use App\Card;
use Tests\TestCase;

/**
 * Class CardTest
 * @package Tests\Feature\TriggerWarning
 */
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
