<?php

namespace controllers;

use mysqli;

class databaseController
{
    private static $instance; // Singleton instance
    private $db;

    private function __construct()
    {
        $this->connectToDatabase();
    }

    private function connectToDatabase()
    {
        $this->db = new mysqli('db', 'root', $_ENV['MYSQL_ROOT_PASSWORD'], $_ENV['MYSQL_DATABASE']);

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // public function getDatabaseConnection()
    // {
    //     return $this->db;
    // }

    public function serializeGameState()
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    public function unserializeGameState($state)
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    public function getPreviousMoves($gameID)
    {
        // Prepare a database query using the connection
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE game_id = ?');

        // Bind the game_id parameter
        $stmt->bind_param('i', $gameID);

        // Execute the database query
        $stmt->execute();
        return $stmt->get_result(); // Get the query result.
    }

    //TODO

    public function makeMove($moveData)
    {
        $gameState = $this->serializeGameState();

        switch ($moveData['type']) {
            case 'pass':
                $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
        VALUES (?, "pass", null, null, ?, ?)');
                $stmt->bind_param('iss', $moveData['gameID'], $moveData['previousMove'], $gameState);
                break;

            case 'move':
                $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
        VALUES (?, "move", ?, ?, ?, ?)');
                $stmt->bind_param('issis', $moveData['gameID'], $moveData['from'], $moveData['to'], $moveData['previousMove'], $gameState);
                break;

            case 'play':
                $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
        VALUES (?, "play", ?, ?, ?, ?)');
                $stmt->bind_param('issis', $moveData['gameID'], $moveData['piece'], $moveData['to'], $moveData['previousMove'], $gameState);
                break;

            case 'undo':
                $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ' . $moveData['previousMove']);
                $stmt->execute();
                return $stmt->get_result()->fetch_array();
                break;

            case 'delete':
                $stmt = $this->db->prepare('DELETE FROM moves WHERE id = ' . $moveData['moveID']);
                $stmt->execute();
                break;

            case 'restart':
                $this->db->prepare('INSERT INTO games VALUES ()')->execute();
                $_SESSION['game_id'] = $this->db->insert_id;
                return $this->db->insert_id;
                break;

            default:
                // Handle unsupported move type
                return false;
        }

        $stmt->execute();
        return $this->db->insert_id;
    }


    // // All possibles moves
    // public function movePass($gameID, $previousMove)
    // {
    //     $gameState = $this->serializeGameState();
    //     $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
    //     VALUES (?, "pass", null, null, ?, ?)');
    //     $stmt->bind_param('iss', $gameID, $previousMove, $gameState);
    //     $stmt->execute();
    //     return $this->db->insert_id;
    // }

    // public function moveMove($gameID, $from, $to, $previousMove)
    // {
    //     $gameState = $this->serializeGameState();
    //     $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
    //     VALUES (?, "move", ?, ?, ?, ?)');
    //     $stmt->bind_param('issis', $gameID, $from, $to, $previousMove, $gameState);
    //     $stmt->execute();
    //     return $this->db->insert_id;
    // }

    // public function movePlay($gameID, $piece, $to, $previousMove)
    // {
    //     $gameState = $this->serializeGameState();
    //     $stmt = $this->db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
    //     VALUES (?, "play", ?, ?, ?, ?)');
    //     $stmt->bind_param('issis', $gameID, $piece, $to, $previousMove, $gameState);
    //     $stmt->execute();
    //     return $this->db->insert_id;
    // }

    // public function moveUndo($previousMove)
    // {
    //     $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ' . $previousMove);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_array();
    // }

    // public function deleteMove($moveID)
    // {
    //     $stmt = $this->db->prepare('DELETE FROM moves WHERE id = ' . $moveID);
    //     $stmt->execute();
    // }

    // public function gameRestart()
    // {
    //     $this->db->prepare('INSERT INTO games VALUES ()')->execute();
    //     $_SESSION['game_id'] = $this->db->insert_id;
    //     return $this->db->insert_id;
    // }
}
