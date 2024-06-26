<?php

namespace components;

class boardComponent
{
    private array $offset;
    private array $board;

    public function __construct($board)
    {
        $this->offset = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
        $this->board = $board;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setBoard($board)
    {
        $this->board = $board;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getAllPossiblePositions()
    {
        $to = [];

        foreach ($this->getOffset() as $pq) {
            foreach (array_keys($this->getBoard()) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
            }
        }

        // Remove duplicate move destinations.
        $to = array_unique($to);

        if (!count($to)) {
            $to[] = '0,0'; // If no move destinations are available, set a default.
        }

        return $to;

        // $allPossible = [];

        // foreach ($to as $position) {
        //     if (!isset($this->board[$position])) {
        //         $allPossible[] = $position;
        //     }
        // }

        // return $allPossible;
    }

    // Function to check if a position on the board has a neighboring position.
    function hasNeighBour($to, $board)
    {
        foreach (array_keys($board) as $b) {
            if ($this->isNeighbour($to, $b)) {
                return true;
            }
        }
    }

    // Function to check if two positions are neighbors on the game board.
    function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        return (
            ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) ||
            ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) ||
            ($a[0] + $a[1] == $b[0] + $b[1])
        );
    }

    // Function to check if neighboring tiles are of the same color.
    function neighboursAreSameColor($player, $to)
    {
        $sameColor = true;

        foreach ($this->board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($to, $b)) {
                $sameColor = false;
            }
        }

        return $sameColor;
    }

    // Function to calculate the length (number of tiles) in a position on the board.
    function len($tile)
    {
        return $tile ? count($tile) : 0;
    }
}
