<?php

use components\boardComponent;
use PHPUnit\Framework\TestCase;

class boardComponentTest extends TestCase
{
    private $boardComponent;

    // Create a board instance to use in tests
    // ARRANGE
    protected function setUp(): void
    {
        $board = [
            '0,0' => [[0, 'A']],
            '1,0' => [[1, 'B']],
        ];

        $this->boardComponent = new boardComponent($board);
    }

    // GOED - Test the getAllPossiblePositions method
    public function testGetAllPossiblePositions()
    {
        $expectedPositions = [
            '0,1', '1,1', '0,-1', '1,-1', '1,0', '2,0', '-1,0',
            '0,0', '-1,1', '2,-1',
        ];

        $positions = $this->boardComponent->getAllPossiblePositions();

        $this->assertEquals(sort($expectedPositions), sort($positions));
    }

    // GOED - Test the hasNeighbour method
    public function testHasNeighbour()
    {
        $this->assertTrue($this->boardComponent->hasNeighBour('1,0'));
        $this->assertFalse($this->boardComponent->hasNeighBour('0,3'));
    }

    // GOED - Test the isNeighbour method
    public function testIsNeighbour()
    {
        $this->assertTrue($this->boardComponent->isNeighbour('1,0', '0,0'));
        $this->assertFalse($this->boardComponent->isNeighbour('2,0', '0,0'));
    }

    // GOED - Test the neighboursAreSameColor method
    public function testNeighboursAreSameColorAsPlayer()
    {

        $this->assertTrue($this->boardComponent->neighboursAreSameColorAsPlayer('1', '2,0'));
        $this->assertFalse($this->boardComponent->neighboursAreSameColorAsPlayer('1', '-1,0'));
    }

    // GOED - Test the len method
    public function testLen()
    {
        $tile1 = [['0', 'A']];
        $tile2 = [['0', 'B'], ['1', 'Q']];

        $this->assertEquals(1, $this->boardComponent->len($tile1));
        $this->assertEquals(2, $this->boardComponent->len($tile2));
    }
}
