<?php

use components\gameComponent;
use components\boardComponent;
use components\playerComponent;
use PHPUnit\Framework\TestCase;

class gameComponentTest extends TestCase
{
    private $gameComponent;

    // Create game, board, and player instances to use in tests
    // ARRANGE
    protected function setUp(): void
    {
        $board = new boardComponent([]); // You can initialize the boardComponent with data if needed
        $player = new playerComponent(0, ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $id = 1;

        $this->gameComponent = new gameComponent($board, $player, $id);
    }

    // Test the getBoard method
    public function testGetBoard()
    {
        $this->assertInstanceOf(boardComponent::class, $this->gameComponent->getBoard());
    }

    // Test the setBoard method
    public function testSetBoard()
    {
        $board = new boardComponent([]); // You can initialize a new boardComponent with different data
        $this->gameComponent->setBoard($board);

        $this->assertInstanceOf(boardComponent::class, $this->gameComponent->getBoard());
    }

    // Test the getId method
    public function testGetId()
    {
        $this->assertEquals(1, $this->gameComponent->getId());
    }

    // Test the setId method
    public function testSetId()
    {
        $this->gameComponent->setId(2);

        $this->assertEquals(2, $this->gameComponent->getId());
    }

    // Test the getPlayer method
    public function testGetPlayer()
    {
        $this->assertInstanceOf(playerComponent::class, $this->gameComponent->getPlayer());
    }

    // Test the setPlayer method
    public function testSetPlayer()
    {
        $player = new playerComponent(0, ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]); // You can create a new playerComponent instance
        $this->gameComponent->setPlayer($player);

        $this->assertInstanceOf(playerComponent::class, $this->gameComponent->getPlayer());
    }
}
