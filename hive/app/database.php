<?php

// Function to serialize and return the current game state
function getState()
{
    return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
}

// Function to unserialize and set the game state based on the provided state
function setState($state)
{
    list($a, $b, $c) = unserialize($state);
    $_SESSION['hand'] = $a;
    $_SESSION['board'] = $b;
    $_SESSION['player'] = $c;
}

// Create a new MySQLi database connection
$db = new mysqli('db', 'root', $_ENV['MYSQL_ROOT_PASSWORD'], $_ENV['MYSQL_DATABASE']);

// Check if the database connection was successful
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Return the database connection object
return $db;
