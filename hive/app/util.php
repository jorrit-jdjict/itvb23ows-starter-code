<?php
// Define global array 'OFFSETS' representing possible neighboring positions.
$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

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

// Function to check if a position on the board has a neighboring position.
function hasNeighBour($a, $board)
{
    foreach (array_keys($board) as $b) {
        if (isNeighbour($a, $b)) {
            return true;
        }
    }
}

// Function to check if neighboring tiles are of the same color.
function neighboursAreSameColor($player, $a, $board)
{
    $sameColor = true;

    foreach ($board as $b => $st) {
        if (!$st) {
            continue;
        }
        $c = $st[count($st) - 1][0];
        if ($c != $player && isNeighbour($a, $b)) {
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

// Function to check if a tile can slide from one position to another on the board.
function slide($board, $from, $to)
{
    $slide = true;

    if (!hasNeighBour($to, $board) || !isNeighbour($from, $to)) {
        $slide = false;
    }

    $b = explode(',', $to);
    $common = [];

    // Check for common neighboring positions between 'from' and 'to'.
    foreach ($GLOBALS['OFFSETS'] as $pq) {
        $p = $b[0] + $pq[0];
        $q = $b[1] + $pq[1];
        if (isNeighbour($from, $p . "," . $q)) {
            $common[] = $p . "," . $q;
        }
    }

    // Check if the slide is possible based on neighboring tiles.
    if ((!isset($board[$common[0]]) || !$board[$common[0]]) &&
        (!isset($board[$common[1]]) || !$board[$common[1]]) &&
        (!isset($board[$from]) || !$board[$from]) &&
        (!isset($board[$to]) || !$board[$to])
    ) {
        $slide = false;
    } else {
        $slide =
            min(
                len($board[$common[0]]),
                len($board[$common[1]])
            ) <= max(
                len($board[$from]),
                len($board[$to])
            );
    }

    return $slide;
}
