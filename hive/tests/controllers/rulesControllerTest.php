<?php

use PHPUnit\Framework\TestCase;
use controllers\rulesController;
use components\boardComponent;

class RulesControllerTest extends TestCase
{
    protected rulesController $rulesController;
    protected $mockBoardComponent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockBoardComponent = $this->createMock(boardComponent::class);

        $this->rulesController = new rulesController($this->mockBoardComponent);
    }

    public function testSlideWithValidSlide()
    {
        // Arrange
        $to = "1,0";
        $from = "0,0";
        $board = [
            "0,1" => [
                0 => [1, "Q"]
            ],
            "0,0" => [
                0 => [0, "Q"]
            ]
        ];

        // Act
        $result = $this->rulesController->slide($from, $to, $board);

        // Assert
        $this->assertTrue($result);
    }
}
