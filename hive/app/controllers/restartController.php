<?php

namespace controllers;

use controllers\databaseController;

class restartController
{
    private databaseController $db;

    public function __construct(databaseController $db)
    {
        $this->db = $db;
    }

    public function restart()
    {
        unset($_SESSION['error']);
        // Initialize an empty game board in the session.
        $_SESSION['board'] = [];

        // Initialize player hands
        $_SESSION['hand'] = [
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], // Initialize player 0's hand with tile counts.
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]  // Initialize player 1's hand with tile counts.
        ];

        // Set the current player to player 0.
        $_SESSION['player'] = 0;

        $args = [
            'type' => 'restart',
        ];

        $this->db->makeMove($args);
    }
}
