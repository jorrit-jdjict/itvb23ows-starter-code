<?php

function getState()
{
    return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
}

function setState($state)
{
    list($a, $b, $c) = unserialize($state);
    $_SESSION['hand'] = $a;
    $_SESSION['board'] = $b;
    $_SESSION['player'] = $c;
}

$db = new mysqli('db', 'root', $_ENV['MYSQL_ROOT_PASSWORD'], 'hive');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

return $db;
