<?php

use PHPUnit\Framework\TestCase;
use components\playerComponent;

class PlayerComponentTest extends TestCase
{
    public function testGetStonesInHandPlayer0()
    {
        // Arrange
        $hand = [
            0 => ["Q" => 0, "B" => 1, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 0, "B" => 0, "S" => 2, "A" => 3, "G" => 3],
        ];

        $expectedResultPlayer0 = [0 => "B", 1 => "S", 2 => "A", 3 => "G"];

        $playerComponentPlayer0 = new playerComponent(0, $hand);

        // Act
        $stonesInHandPlayer0 = $playerComponentPlayer0->getStonesInHand();

        // Assert
        $this->assertEquals($expectedResultPlayer0, $stonesInHandPlayer0);
    }

    public function testGetStonesInHandPlayer1()
    {
        // Arrange
        $hand = [
            0 => ["Q" => 0, "B" => 1, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 0, "B" => 0, "S" => 2, "A" => 3, "G" => 3],
        ];

        $expectedResultPlayer1 = [0 => "S", 1 => "A", 2 => "G"];

        $playerComponentPlayer1 = new playerComponent(1, $hand);

        // Act
        $stonesInHandPlayer1 = $playerComponentPlayer1->getStonesInHand();

        // Assert
        $this->assertEquals($expectedResultPlayer1, $stonesInHandPlayer1);
    }

    public function testGetStonesOfPlayer0OnBoard()
    {
        // Arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q"]],
        ];

        $playerComponent = new playerComponent(
            0,
            [
                [
                    "Q" => 0,
                    "B" => 2,
                    "S" => 2,
                    "A" => 3,
                    "G" => 3
                ],
                [
                    "Q" => 0,
                    "B" => 2,
                    "S" => 2,
                    "A" => 3,
                    "G" => 3
                ]
            ]
        );

        // Act
        $result = $playerComponent->getStonesOfPlayerOnBoard($board);

        // Assert
        $this->assertEquals([
            "0,0" =>
            [
                [
                    0 => 0,
                    1 => "Q"
                ]
            ]
        ], $result);
    }

    public function testGetStonesOfPlayer1OnBoard()
    {
        // Arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "Q"]],
        ];

        $playerComponent = new playerComponent(
            1,
            [
                [
                    "Q" => 0,
                    "B" => 2,
                    "S" => 2,
                    "A" => 3,
                    "G" => 3
                ],
                [
                    "Q" => 0,
                    "B" => 2,
                    "S" => 2,
                    "A" => 3,
                    "G" => 3
                ]
            ]
        );

        // Act
        $result = $playerComponent->getStonesOfPlayerOnBoard($board);

        // Assert
        $this->assertEquals([
            "0,1" =>
            [
                [
                    0 => 1,
                    1 => "Q"
                ]
            ]
        ], $result);
    }
}
