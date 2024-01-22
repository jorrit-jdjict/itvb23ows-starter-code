<?php

class GameDatabase
{
    private static $instance; // Singleton instance
    private $db;

    private function __construct()
    {
        $this->connectToDatabase();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connectToDatabase()
    {
        $this->db = new mysqli('db', 'root', $_ENV['MYSQL_ROOT_PASSWORD'], $_ENV['MYSQL_DATABASE']);

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getDatabaseConnection()
    {
        return $this->db;
    }

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
}
