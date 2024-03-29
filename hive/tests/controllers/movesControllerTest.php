<?php

use PHPUnit\Framework\TestCase;
use controllers\movesController;
use controllers\databaseController;
use components\boardComponent;
use components\playerComponent;
use components\gameComponent;
use controllers\rulesController;

class MovesControllerTest extends TestCase
{
    protected movesController $movesController;
    protected rulesController $rulesController;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocked instances of dependencies
        $mockDb = $this->createMock(databaseController::class);
        $mockBoard = $this->createMock(boardComponent::class);
        $mockPlayerComponent = $this->createMock(playerComponent::class);
        $mockGame = $this->createMock(gameComponent::class);
        $mockRulesController = $this->createMock(rulesController::class);

        // Create an instance of the movesController class with mocked dependencies
        $this->movesController = new movesController($mockDb, $mockBoard, $mockPlayerComponent, $mockGame, $mockRulesController);

        // Set up session variables if needed
        $_SESSION['last_move'] = 123; // Example value for last move
    }

    public function testEmptyMove()
    {
        // Arrange
        $from = '0,0';
        $to = '0,1';

        // Act
        $this->movesController->move($from, $to);

        // Assert
        $this->assertArrayHasKey('error', $_SESSION);
    }

    // public function testPass()
    // {
    //     // Arrange

    //     // Act

    //     // Assert
    // }

    // public function testPlay()
    // {
    //     // Arrange

    //     // Act

    //     // Assert
    // }

    public function testRestart()
    {
        // Arrange
        $_SESSION['error'] = 'Some error';
        $_SESSION['board'] = ['some' => 'board'];
        $_SESSION['hand'] = [0 => ['some' => 'hand'], 1 => ['some' => 'hand']];
        $_SESSION['player'] = 1;

        // Act
        $this->movesController->restart();

        // Assert
        $this->assertArrayNotHasKey('error', $_SESSION);
        $this->assertEquals([], $_SESSION['board']);
        $this->assertEquals([
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ], $_SESSION['hand']);
        $this->assertEquals(0, $_SESSION['player']);
    }

    // public function testUndo()
    // {
    //     // Arrange

    //     // Act

    //     // Assert
    // }
}
