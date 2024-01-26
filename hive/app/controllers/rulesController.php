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

    public function antSlide($from, $to)
    {
        // Remove $from tile from board array
        unset($this->boardComponent[$from]);

        $visited = [];
        $tiles = array($from);

        // Find if path exists between $from and $to using DFS
        while (!empty($tiles)) {
            $currentTile = array_shift($tiles);

            if (!in_array($currentTile, $visited)) {
                $visited[] = $currentTile;
            }

            $b = explode(',', $currentTile);

            // Put all adjacent legal board positions relative to current tile in $tiles array
            foreach ($this->boardComponent->getOffset() as $pq) {
                $p = $b[0] + $pq[0];
                $q = $b[1] + $pq[1];

                $position = $p . "," . $q;

                if (
                    !in_array($position, $visited) &&
                    !isset($this->boardComponent[$position]) &&
                    $this->boardComponent->hasNeighbour($this->boardComponent, $position)
                ) {
                    if ($position == $to) {
                        return true;
                    }
                    $tiles[] = $position;
                }
            }
        }

        return false;
    }

    public function spiderSlide($from, $to): bool
    {

        // Remove $from tile from board array
        unset($this->boardComponent[$from]);

        $visited = [];
        $tiles = array($from);
        $tiles[] = null;

        $prevTile = null;
        $depth = 0;

        // Find if path exists between $from and $to using DFS with move limit
        while (
            !empty($tiles) &&
            $depth < 3
        ) {
            $currentTile = array_shift($tiles);

            // Null is added to $tiles array to indicate increase in depth
            if ($currentTile == null) {
                $depth++;
                $tiles[] = null;
                if (reset($tiles) == null) { // Double null = all nodes have been visited
                    break;
                } else {
                    continue;
                }
            }

            if (!in_array($currentTile, $visited)) {
                $visited[] = $currentTile;
            }

            $b = explode(',', $currentTile);

            // Put all adjacent legal board positions relative to current tile in $tiles array
            foreach ($this->boardComponent->getOffset() as $pq) {
                $p = $b[0] + $pq[0];
                $q = $b[1] + $pq[1];

                $position = $p . "," . $q;

                if (
                    !in_array($position, $visited) &&
                    $position != $prevTile &&           // Don't move back to previous position
                    !isset($board[$position]) &&
                    $this->boardComponent->hasNeighbour($this->boardComponent, $position)
                ) {
                    if (
                        $position == $to &&
                        $depth == 2
                    ) {
                        return true;
                    }
                    $tiles[] = $position;
                }
            }

            $prevTile = $currentTile;
        }

        return false;
    }

    public function grasshopperSlide($from, $to): bool
    {

        $fromExploded = explode(',', $from);
        $toExploded = explode(',', $to);

        // Get direction to move in to reach $to
        if ($fromExploded[1] == $toExploded[1]) {           // -- On same horizontal axis --
            if ($fromExploded[0] > $toExploded[0]) {        // R -> L
                $offset = [-1, 0];
            } else {                                        // L -> R
                $offset = [1, 0];
            }
        } elseif ($fromExploded[0] == $toExploded[0]) {     // -- On same TL - BR diagonal axis --
            if ($fromExploded[1] > $toExploded[1]) {        // BR -> TL
                $offset = [0, -1];
            } else {                                        // TL -> BR
                $offset = [0, 1];
            }
        } elseif (                                          // -- On same TR - BL diagonal axis --
            $fromExploded[1] == $toExploded[1] -
            ($fromExploded[0] - $toExploded[0])
        ) {
            if ($fromExploded[0] > $toExploded[0]) {        // TR -> BL
                $offset = [-1, 1];
            } else {                                        // BL -> TR
                $offset = [1, -1];
            }
        } else {
            return false;
        }

        $p = $fromExploded[0] + $offset[0];
        $q = $fromExploded[1] + $offset[1];

        $position = $p . "," . $q;
        $positionExploded = [$p, $q];

        // Don't allow moving to empty neighbours
        if (!isset($this->boardComponent[$position])) {
            return false;
        }

        // Set $position to first empty position found when following offset
        while (isset($this->boardComponent[$position])) {
            $p = $positionExploded[0] + $offset[0];
            $q = $positionExploded[1] + $offset[1];

            $position = $p . "," . $q;
            $positionExploded = [$p, $q];
        }

        if ($position == $to) {
            return true;
        }
        return false;
    }
}
