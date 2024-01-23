<?php

namespace components;

class playerComponent
{
    private $hand;
    private $playerID;

    public function __construct($playerID, $hand)
    {
        $this->hand = $hand;
        $this->playerID = $playerID;
    }
    public function getHand()
    {
        return $this->hand;
    }

    public function setHand($hand)
    {
        $this->hand = $hand;
    }

    public function getPlayerID()
    {
        return $this->playerID;
    }

    public function setPlayerID($playerID)
    {
        $this->playerID = $playerID;
    }

    // Get all available stones in hand
    // TODO deze geeft nu 2 handen terug, moet maar 1 zijn, van de player die aangeroepen wordt
    public function getStonesInHand()
    {
        $tiles = [];

        foreach ($this->hand[$this->playerID] as $tile => $ct) {
            if ($ct > 0) {
                $tiles[] = $tile;
            }
        }
        // var_dump($$this->hand[$this->playerID]);

        return $tiles;
    }

    // Get all stones of player which are on the board
    public function getStonesOfPlayerOnBoard($board)
    {
        $tiles = [];

        foreach ($board as $tile => $pos) {
            if ($pos[0][0] == $this->playerID) {
                $tiles[$tile] = $pos;
            }
        }

        return $tiles;
    }

    // Get all the possible positions for a player
    public function getAllPossiblePositionsForPlayer($board)
    {
        $to = [];

        foreach ($board->getOffset() as $pq) {
            foreach (array_keys($board->getBoard()) as $pos) {
                $pq2 = explode(',', $pos);
                if ($pq[0] + $pq2[0] === $this->playerID) {
                    $position = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                    if (!isset($board->getBoard()[$position])) {
                        $to[] = $position;
                    }
                }
            }
        }

        // Remove duplicate move destinations.
        $to = array_unique($to);

        if (!count($to)) {
            $to[] = '0,0'; // If no move destinations are available, set a default.
        }

        return $to;
    }
}
