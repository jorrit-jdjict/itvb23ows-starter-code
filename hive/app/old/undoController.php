<?php

namespace controllers;

use controllers\databaseController;
use components\gameComponent;

class undoController
{
    private databaseController $db;
    private gameComponent $game;

    public function __construct(databaseController $db, gameComponent $game)
    {
        $this->db = $db;
        $this->game = $game;
    }

    public function undoMove()
    {
        if (count($this->game->getBoard()->getBoard()) != 0) {
            $args = [
                'type' => 'undo',
                'previousMove' => $_SESSION['last_move'],
            ];

            $result = $this->db->makeMove($args);


            $args = [
                'type' => 'delete',
                'moveID' => $_SESSION['last_move'],
            ];

            $this->db->makeMove($args);

            $_SESSION['last_move'] = $result[5];
            $this->db->unserializeGameState($result[6]);

            $_SESSION['player'] = 1 - $_SESSION['player'];
        }
    }
}
