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

        try {

            Card::smartRandom()->get();

            $this->assertTrue(true);

        } catch (\Exception $ex) {

            $this->assertTrue(false);

        }

    }

    /**
     * @test
     */
    public function replacePlaceholdersTest()
    {

        /** @var Card $card */
        $card = factory(Card::class)->create();

        $card->original_content = "AAA" . Card::NAME_PLACEHOLDER . "BBB";
        $card->replacePlaceholders();

        $this->assertStringContainsString("AAA", $card->content);
        $this->assertStringNotContainsString(Card::NAME_PLACEHOLDER, $card->content);
        $this->assertStringContainsString("BBB", $card->content);

    }

    /**
     * @test
     */
    public function getSpacesCountAttributeTest()
    {
        /** @var Card $card */
        $card = factory(Card::class)->create();

        $card->content = "AAABBB";
        $this->assertEquals(0, $card->spaces_count);

        $card->content = "AAA@BBB";
        $this->assertEquals(1, $card->spaces_count);

        $card->content = "AAA@BBB@CCC";
        $this->assertEquals(2, $card->spaces_count);
    }

}
