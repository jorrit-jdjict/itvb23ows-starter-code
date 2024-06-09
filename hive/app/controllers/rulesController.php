<?php

namespace controllers;

use components\boardComponent;

class rulesController
{
    private boardComponent $boardComponent;

    public function __construct(boardComponent $boardComponent)
    {
        $this->boardComponent = $boardComponent;
    }

    // Function to check if a tile can slide from one position to another on the board.
    public function slide($from, $to, $board)
    {
        $slide = true;

        if (isset($_SESSION['hand'][$_SESSION['player']]['Q']) && $_SESSION['hand'][$_SESSION['player']]['Q']) {
            $slide = false;
        }


        if (!$this->boardComponent->hasNeighBour($to, $board) || !$this->boardComponent->isNeighbour($from, $to)) {
            $slide = false;
        }

        $b = explode(',', $to);
        $common = [];

        // Check for common neighboring positions between 'from' and 'to'.
        foreach ($this->boardComponent->getOffset() as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->boardComponent->isNeighbour($from, $p . "," . $q)) {
                $common[] = $p . "," . $q;
            }
        }

        // var_dump($this->boardComponent->getBoard());

        // Check if the slide is possible based on neighboring tiles.
        if ((!isset($board[$common[0]]) || !$board[$common[0]]) &&
            (!isset($board[$common[1]]) || !$board[$common[1]]) &&
            (!isset($board[$from]) || !$board[$from]) &&
            (!isset($board[$to]) || !$board[$to])
        ) {
            $slide = false;
        } else {
            $firstCommonLen = $board[$common[0]] ?? 0;
            $firstCommonLen = $this->boardComponent->len($firstCommonLen);

            $secondCommonLen = $board[$common[1]] ?? 0;
            $secondCommonLen = $this->boardComponent->len($secondCommonLen);

            $fromLen = $board[$from] ?? 0;
            $fromLen = $this->boardComponent->len($fromLen);

            $toLen = $board[$to] ?? 0;
            $toLen = $this->boardComponent->len($toLen);

            $slide =
                min($firstCommonLen, $secondCommonLen)
                <= max($fromLen, $toLen);
        }

        // var_dump($this->boardComponent);
        // var_dump($slide);

        return $slide;

        // else {
        //     $slide =
        //         min(
        //             $this->len($this->board[$common[0]]),
        //             $this->len($this->board[$common[1]])
        //         ) <= max(
        //             $this->len($this->board[$from]),
        //             $this->len($this->board[$to])
        //         );
        // }

        // return $slide;
    }

    public function antSoldierSlide($from, $to, $board)
    {
        // Cant move to the same tile
        if ($from == $to) {
            return false;
        }

        // Cant move to tile which is occupied
        if (isset($board[$to])) {
            return false;
        }

        return $this->slide($from, $to, $board);
    }

    public function GrassHopperSlide($from, $to, $board): bool
    {

        // Feature request 1b
        // b. Een sprinkhaan mag zich niet verplaatsen naar het veld waar hij al staat.
        if ($from == $to) {
            return false;
        }

        // d. Een sprinkhaan mag niet naar een bezet veld springen.
        if (isset($board[$to])) {
            return false;
        }

        // a. Een sprinkhaan verplaatst zich door in een rechte lijn een sprong te maken
        // naar een veld meteen achter een andere steen in de richting van de sprong.
        $fromExploded = explode(',', $from);
        $toExploded = explode(',', $to);

        $allowedAbsoluteDirections = [
            [0, 1],
            [1, 1],
            [1, 0]
        ];

        $distanceP = $toExploded[0] - $fromExploded[0];
        $distanceQ = $toExploded[1] - $fromExploded[1];

        $absoluteMovement = [abs($distanceP), abs($distanceQ)];

        foreach ($allowedAbsoluteDirections as $direction) {
            if ($direction[0] == 0) {
                // Basecase vertical movement
                if ($absoluteMovement[0] == 0 && $absoluteMovement[1] % $direction[1] == 0) {
                    // c. Een sprinkhaan moet over minimaal één steen springen.
                    if ($absoluteMovement[1] > 1) {
                        // e. Een sprinkhaan mag niet over lege velden springen. Dit betekent dat alle
                        // velden tussen de start- en eindpositie bezet moeten zijn.
                        // 0,1 > 0,4
                        // var_dump($fromExploded[1], $toExploded[1]);
                        // if ($fromExploded[1] > $toExploded[1]) {
                        //     // Positive distance
                        //     // var_dump($distanceQ);
                        //     for ($j = 0; $j < abs($distanceQ); $j++) {
                        //         $Qstep = implode(',', [intval($toExploded[0]), intval($toExploded[1]) + $j]);
                        //         if ($to != $Qstep && !isset($board[$Qstep])) {
                        //             return false;
                        //         }
                        //     }
                        // } else {
                        //     // Negative distance
                        //     for ($k = 0; $k > abs($distanceQ); $k++) {
                        //         $Qstep = implode(',', [intval($toExploded[0]), intval($toExploded[1]) - $k]);
                        //         if ($to != $Qstep && !isset($board[$Qstep])) {
                        //             return false;
                        //         }
                        //     }
                        // }

                        return true;
                    }
                }
            } elseif ($direction[1] == 0) {
                // var_dump("help: " . $from);
                // Basecase horizontal movement
                if ($absoluteMovement[1] == 0 && $absoluteMovement[0] % $direction[0] == 0) {
                    // c. Een sprinkhaan moet over minimaal één steen springen.
                    if ($absoluteMovement[0] > 1) {
                        // e. Een sprinkhaan mag niet over lege velden springen. Dit betekent dat alle
                        // velden tussen de start- en eindpositie bezet moeten zijn. 
                        // if ($fromExploded[0] > $toExploded[0]) {
                        //     // Positive distance
                        //     // var_dump($distanceQ);
                        //     for ($h = 0; $h < abs($distanceP); $h++) {
                        //         $Pstep = implode(',', [intval($toExploded[0]) + $h, intval($toExploded[1])]);
                        //         // var_dump($to != $Pstep && !isset($board[$Pstep]));
                        //         if ($to != $Pstep && !isset($board[$Pstep])) {
                        //             var_dump('aaaaa');
                        //             return false;
                        //         }
                        //     }
                        // } else {
                        //     // Negative distance
                        //     for ($l = 0; $l > abs($distanceP); $l++) {
                        //         $Pstep = implode(',', [intval($toExploded[0]) - $l, intval($toExploded[1])]);
                        //         if ($to != $Pstep && !isset($board[$Pstep])) {
                        //             return false;
                        //         }
                        //     }
                        // }

                        return true;
                    }
                }
            } else {
                // Diagonal movement
                if ($absoluteMovement[0] % $direction[0] == 0 && $absoluteMovement[1] % $direction[1] == 0) {
                    if ($absoluteMovement[0] / $direction[0] == $absoluteMovement[1] / $direction[1]) {
                        // c. Een sprinkhaan moet over minimaal één steen springen.
                        if ($absoluteMovement[0] > 1) {
                            // e. Een sprinkhaan mag niet over lege velden springen. Dit betekent dat alle
                            // velden tussen de start- en eindpositie bezet moeten zijn. 
                            // if ($fromExploded[0] > $toExploded[0]) {
                            //     // Positive distance
                            //     // var_dump($distanceQ);
                            //     for ($m = 0; $m <= abs($distanceP); $m++) {
                            //         $Pstep = implode(',', [intval($toExploded[0]) + $m, intval($toExploded[1]) + $m]);
                            //         var_dump("m="  . $m . " a from: " . $from . " to: " . $to . ' pstep= ' . $Pstep . ' distancep: ' . abs($distanceP));
                            //         var_dump($board[$Pstep]);
                            //         if ($to != $Pstep && !isset($board[$Pstep])) {
                            //             var_dump('aaaaa');
                            //             return false;
                            //         }
                            //     }
                            // } else {
                            //     // Negative distance
                            //     for ($n = 0; $n >= abs($distanceP); $n++) {
                            //         $Pstep = implode(',', [intval($toExploded[0]) - $n, intval($toExploded[1]) - $n]);
                            //         var_dump("b from: " . $from . " to: " . $to . ' pstep= ' . $Pstep);
                            //         if ($to != $Pstep && !isset($board[$Pstep])) {
                            //             return false;
                            //         }
                            //     }
                            // }

                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
