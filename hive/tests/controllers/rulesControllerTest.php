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
        $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "Q"]],
            "0,1" => [[0, "B"]],
            "-1,-1" => [[1, "B"]],
            "1,1" => [[0, "B"]],
            "-2,0" => [[1, "B"]],
            "-2,-1" => [[0, "S"]],
            "-2,1" => [[1, "S"]],
            "0,2" => [[0, "G"]],
            "-1,1" => [[1, "G"]]
        ];

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
        $resultBottomRight = $this->rulesController->isSlideStraightLines($fromBottomRight, $toBottomRight);
        // Direction Bottom slide
        $resultBottom = $this->rulesController->isSlideStraightLines($fromBottom, $toBottom);
        // Direction BottomLeft slide
        $resultBottomLeft = $this->rulesController->isSlideStraightLines($fromBottomLeft, $toBottomLeft);
        // Direction TopLeft slide
        $resultTopLeft = $this->rulesController->isSlideStraightLines($fromTopLeft, $toTopLeft);
        // Direction Top slide
        $resultTop = $this->rulesController->isSlideStraightLines($fromTop, $toTop);
        // Direction TopRight slide
        $resultTopRight = $this->rulesController->isSlideStraightLines($fromTopRight, $toTopRight);


        // Assert
        $this->assertTrue($resultBottomRight);
        $this->assertTrue($resultBottom);
        $this->assertTrue($resultBottomLeft);
        $this->assertTrue($resultTopLeft);
        $this->assertTrue($resultTop);
        $this->assertTrue($resultTopRight);
    }

    public function testGrasshopperSlideNonStraightlines()
    {
        // Arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "Q"]],
            "0,1" => [[0, "B"]],
            "-1,-1" => [[
                1, "B"
            ]],
            "1,1" => [[0, "B"]],
            "-2,0" => [[1, "B"]],
            "-2,-1" => [[
                0, "S"
            ]],
            "-2,1" => [[1, "S"]],
            "0,2" => [[0, "G"]],
            "-1,1" => [[1, "G"]]
        ];
        // These lines should not be allowed, since a grasshopper can only travel in a straight line
        $from1 = "0,0";
        $to1 = "2,4";

        $from2 = "0,0";
        $to2 = "1,3";

        $from3 = "0,0";
        $to3 = "-4,2";

        $from4 = "0,0";
        $to4 = "2,3";

        // Act
        // Test the invalid moves
        $result1 = $this->rulesController->isSlideStraightLines($from1, $to1);
        $result2 = $this->rulesController->isSlideStraightLines($from2, $to2);
        $result3 = $this->rulesController->isSlideStraightLines($from3, $to3);
        $result4 = $this->rulesController->isSlideStraightLines($from4, $to4);

        // Assert
        // They should all be false, since they are invalid moves
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
    }

    // Feature Request 1B
    public function testGrasshopperSlideToSameTile()
    {
        // Arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "Q"]],
            "0,1" => [[0, "B"]],
            "-1,-1" => [[
                1, "B"
            ]],
            "1,1" => [[0, "B"]],
            "-2,0" => [[1, "B"]],
            "-2,-1" => [[
                0, "S"
            ]],
            "-2,1" => [[1, "S"]],
            "0,2" => [[0, "G"]],
            "-1,1" => [[1, "G"]]
        ];

        $from = "0,2";
        $to = '0,2';

        // Act
        $result = $this->rulesController->GrasshopperSlide($from, $to, $board);

        // Assert
        $this->assertFalse($result);
    }
}
