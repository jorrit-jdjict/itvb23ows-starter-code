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

    // Feature Request 1B Een sprinkhaan mag zich niet verplaatsen naar het veld waar hij al staat.
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

    // Feature request 1 a. Een sprinkhaan verplaatst zich door in een rechte lijn een sprong te maken
    // naar een veld meteen achter een andere steen in de richting van de sprong.
    public function testGrasshopperSlideValidMoves()
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

        // Direction TopLeft slide
        $fromTopLeft = "0,2";
        $toTopLeft = "0,-1";
        // Direction Top slide
        $fromTop = "0,2";
        $toTop = "-3,-1";
        // Direction TopRight slide
        $fromTopRight = "-1,1";
        $toTopRight = "-3,1";
        // Direction BottomLeft slide
        $fromBottomLeft  = "-1,1";
        $toBottomLeft  = "2,1";
        // Direction Bottom slide
        $fromBottom = "-1,1";
        $toBottom = "1,3";

        // Act
        // Direction TopLeft slide
        $resultBottomRight = $this->rulesController->GrassHopperSlide($fromTopLeft, $toTopLeft, $board);
        /// Direction Top slide
        $resultBottom = $this->rulesController->GrassHopperSlide($fromTop, $toTop, $board);
        // Direction TopRight slide
        $resultBottomLeft = $this->rulesController->GrassHopperSlide($fromTopRight, $toTopRight, $board);
        // Direction BottomLeft slide
        $resultTopLeft = $this->rulesController->GrassHopperSlide($fromBottomLeft, $toBottomLeft, $board);
        // Direction Bottom slide
        $resultTop = $this->rulesController->GrassHopperSlide($fromBottom, $toBottom, $board);

        // Assert
        $this->assertTrue($resultBottomRight);
        $this->assertTrue($resultBottom);
        $this->assertTrue($resultBottomLeft);
        $this->assertTrue($resultTopLeft);
        $this->assertTrue($resultTop);
    }

    // Feature request 1 a. Een sprinkhaan verplaatst zich door in een rechte lijn een sprong te maken
    // naar een veld meteen achter een andere steen in de richting van de sprong.
    public function testGrasshopperSlideInvalidMoves()
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

        $from1 = "0,2";
        $to1 = "-1,-1";
        $from2 = "0,2";
        $to2 = "-2,-1";
        $from3 = "-1,1";
        $to3 = "-3,0";
        $from4  = "-1,1";
        $to4  = "2,0";
        $from5 = "-1,1";
        $to5 = "1,2";

        // Act
        $result1 = $this->rulesController->GrassHopperSlide($from1, $to1, $board);
        $result2 = $this->rulesController->GrassHopperSlide($from2, $to2, $board);
        $result3 = $this->rulesController->GrassHopperSlide($from3, $to3, $board);
        $result4 = $this->rulesController->GrassHopperSlide($from4, $to4, $board);
        $result5 = $this->rulesController->GrassHopperSlide($from5, $to5, $board);

        // Assert
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
        $this->assertFalse($result5);
    }

    // c. Een sprinkhaan moet over minimaal één steen springen.
    public function testGrasshopperSlide1StoneRule()
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

        // These lines should not be allowed, since a grasshopper should always jump atleast 1 stone
        $from1 = "0,2";
        $to1 = "-1,2";

        $from2 = "0,2";
        $to2 = "1,2";

        $from3 = "-1,1";
        $to3 = "-1,2";

        $from4 = "0,2";
        $to4 = "2,2";

        // Act
        // Test the invalid moves
        $result1 = $this->rulesController->GrassHopperSlide($from1, $to1, $board);
        $result2 = $this->rulesController->GrassHopperSlide($from2, $to2, $board);
        $result3 = $this->rulesController->GrassHopperSlide($from3, $to3, $board);
        $result4 = $this->rulesController->GrassHopperSlide($from4, $to4, $board);

        // Assert
        // They should all be false, since they are invalid moves
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
    }

    // d. Een sprinkhaan mag niet naar een bezet veld springen.
    public function testGrasshopperSlideToFilledSpace()
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

        // These lines should not be allowed, since a grasshopper is not allowed to jump on a filled tile
        $from1 = "0,2";
        $to1 = "0,1";

        $from2 = "0,2";
        $to2 = "0,0";

        $from3 = "-1,1";
        $to3 = "-2,0";

        $from4 = "-1,1";
        $to4 = "1,1";

        // Act
        // Test the invalid moves
        $result1 = $this->rulesController->GrassHopperSlide($from1, $to1, $board);
        $result2 = $this->rulesController->GrassHopperSlide($from2, $to2, $board);
        $result3 = $this->rulesController->GrassHopperSlide($from3, $to3, $board);
        $result4 = $this->rulesController->GrassHopperSlide($from4, $to4, $board);

        // Assert
        // They should all be false, since they are invalid moves
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
        $this->assertFalse($result4);
    }

    // e. Een sprinkhaan mag niet over lege velden springen. Dit betekent dat alle
    // velden tussen de start- en eindpositie bezet moeten zijn. 

    public function testGrasshopperSlideOverEmptySpaces()
    {
        // Arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "-1,0" => [[1, "Q"]],
            "0,1" => [[0, "B"]],
            "1,1" => [[0, "B"]],
            "-2,0" => [[1, "B"]],
            "-2,-1" => [[0, "S"]],
            "-2,1" => [[1, "S"]],
            "-3,1" => [[1, "G"]],
            "0,2" => [[0, "G"]],
            "-3,-1" => [[1, "B"]]
        ];

        $from1 = "-3,1";
        $to1 = "-3,-2";
        $from2 = "-3,1";
        $to2 = "2,1";
        $from3 = "0,1";
        $to3 = "-4,-2";

        // Act
        $result1 = $this->rulesController->GrassHopperSlide($from1, $to1, $board);
        $result2 = $this->rulesController->GrassHopperSlide($from2, $to2, $board);
        $result3 = $this->rulesController->GrassHopperSlide($from3, $to3, $board);

        // Assert
        // They all pass an empty square, so they should all be false.
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
    }
}
