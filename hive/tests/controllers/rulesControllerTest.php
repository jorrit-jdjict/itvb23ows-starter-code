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

    public function testGrasshopperSlideStraightlines()
    {
        // Arrange
        // Direction BottomRight slide
        $fromBottomRight = "0,0";
        $toBottomRight = "0,4";
        // Direction Bottom slide
        $fromBottom = "0,0";
        $toBottom = "4,4";
        // Direction BottomLeft slide
        $fromBottomLeft = "0,0";
        $toBottomLeft = "4,0";
        // Direction TopLeft slide
        $fromTopLeft = "0,4";
        $toTopLeft = "0,1";
        // Direction Top slide
        $fromTop = "4,4";
        $toTop = "0,0";
        // Direction TopRight slide
        $fromTopRight = "4,0";
        $toTopRight = "0,0";


        // Act
        // Direction BottomRight slide
        $resultBottomRight = $this->rulesController->GrasshopperSlide($fromBottomRight, $toBottomRight);
        // Direction Bottom slide
        $resultBottom = $this->rulesController->GrasshopperSlide($fromBottom, $toBottom);
        // Direction BottomLeft slide
        $resultBottomLeft = $this->rulesController->GrasshopperSlide($fromBottomLeft, $toBottomLeft);
        // Direction TopLeft slide
        $resultTopLeft = $this->rulesController->GrasshopperSlide($fromTopLeft, $toTopLeft);
        // Direction Top slide
        $resultTop = $this->rulesController->GrasshopperSlide($fromTop, $toTop);
        // Direction TopRight slide
        $resultTopRight = $this->rulesController->GrasshopperSlide($fromTopRight, $toTopRight);


        // Assert
        $this->assertTrue($resultBottomRight);
        $this->assertTrue($resultBottom);
        $this->assertTrue($resultBottomLeft);
        $this->assertTrue($resultTopLeft);
        $this->assertTrue($resultTop);
        $this->assertTrue($resultTopRight);
    }
}
