<?php

use components\boardComponent;
use components\playerComponent;
use PHPUnit\Framework\TestCase;

class playerComponentTest extends TestCase
{
    private $playerComponent;

    // Create a player instance to use in tests
    // ARRANGE
    protected function setUp(): void
    {
        $playerID = 1;
        $hand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];

        $this->playerComponent = new playerComponent($playerID, $hand);
    }

    // Test the getHand method
    public function testGetHand()
    {
        $expectedHand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
        $this->assertEquals($expectedHand, $this->playerComponent->getHand());
    }

    // Test the setHand method
    public function testSetHand()
    {
        $newHand = ["Q" => 2, "B" => 1, "S" => 3, "A" => 2, "G" => 4];
        $this->playerComponent->setHand($newHand);
        $this->assertEquals($newHand, $this->playerComponent->getHand());
    }

    // Test the getPlayerID method
    public function testGetPlayerID()
    {
        $this->assertEquals(1, $this->playerComponent->getPlayerID());
    }

    // Test the setPlayerID method
    public function testSetPlayerID()
    {
        $newPlayerID = 2;
        $this->playerComponent->setPlayerID($newPlayerID);
        $this->assertEquals($newPlayerID, $this->playerComponent->getPlayerID());
    }

    // Test the getStonesInHand method
    public function testGetStonesInHand()
    {
        // The expected result should be an associative array with stone names as keys and counts as values.
        $expectedStones = [
            "Q" => 1,
            "B" => 2,
            "S" => 2,
            "A" => 3,
            "G" => 3,
        ];

        $this->assertEquals(json_encode($expectedStones), json_encode($this->playerComponent->getStonesInHand()));
    }

    // Test the getStonesOfPlayerOnBoard method
    public function testGetStonesOfPlayerOnBoard()
    {
        $board = [
            '0,0' => [[0, 'A']],
            '1,0' => [[1, 'B']],
            '1,1' => [[1, 'S']],
            '2,2' => [[0, 'G']],
        ];

        $expectedStonesOnBoard = [
            '0,0' => [[0, 'A']],
            '1,0' => [[1, 'B']],
            '1,1' => [[1, 'S']],
        ];

        $this->assertEquals($expectedStonesOnBoard, $this->playerComponent->getStonesOfPlayerOnBoard($board));
    }

    // Test the getAllPossiblePositionsForPlayer method
    public function testGetAllPossiblePositionsForPlayer()
    {
        $board = new boardComponent([
            '0,0' => [[0, 'A']],
            '0,1' => [[1, 'B']],
            '1,0' => [[0, 'S']],
            '2,2' => [[1, 'G']],
        ]);

        $expectedPossiblePositions = ['0,2', '1,1', '1,2', '2,0', '2,1'];
        $this->assertEquals($expectedPossiblePositions, $this->playerComponent->getAllPossiblePositionsForPlayer($board));
    }
}
