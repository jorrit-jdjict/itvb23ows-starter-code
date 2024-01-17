<?php
class GameBoard
{
    private $board;
    private $player;

    // All possible directions to move in relative to a position
    private array $offsets =
    [
        [0, 1],
        [0, -1],
        [1, 0],
        [-1, 0],
        [-1, 1],
        [1, -1]
    ];

    public function getOffsets(): array
    {
        return $this->offsets;
    }

    public function __construct()
    {
        $this->initializeBoard();
    }

    private function initializeBoard()
    {
        $this->board = [];
        $this->player = 0;
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        return (
            ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) ||
            ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) ||
            ($a[0] + $a[1] == $b[0] + $b[1])
        );
    }

    function hasNeighbour($board, $a)
    {
        list($x, $y) = explode(',', $a);

        foreach ($this->offsets as [$dx, $dy]) {
            $nx = $x + $dx;
            $ny = $y + $dy;
            $neighbourPosition = "$nx,$ny";

            if (isset($board[$neighbourPosition]) && $this->isNeighbour($a, $neighbourPosition)) {
                return true;
            }
        }

        return false;
    }

    public function neighboursAreSameColor($a)
    {
        $sameColor = true;

        foreach ($this->board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $this->player && $this->isNeighbour($a, $b)) {
                $sameColor = false;
            }
        }

        return $sameColor;
    }

    public function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    public function slide($from, $to)
    {
        $slide = true;
        $board = $this->getBoard();

        if (!$this->hasNeighbour($board, $to) || !$this->isNeighbour($from, $to)) {
            $slide = false;
        }

        $b = explode(',', $to);
        $common = [];

        foreach ($this->offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . "," . $q)) {
                $common[] = $p . "," . $q;
            }
        }

        if ((!isset($this->board[$common[0]]) || !$this->board[$common[0]]) &&
            (!isset($this->board[$common[1]]) || !$this->board[$common[1]]) &&
            (!isset($this->board[$from]) || !$this->board[$from]) &&
            (!isset($this->board[$to]) || !$this->board[$to])
        ) {
            $slide = false;
        } else {
            $slide =
                min(
                    $this->len($this->board[$common[0]]),
                    $this->len($this->board[$common[1]])
                ) <= max(
                    $this->len($this->board[$from]),
                    $this->len($this->board[$to])
                );
        }

        return $slide;
    }
}
