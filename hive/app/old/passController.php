<?php

// namespace controllers;

// use components\playerComponent;
// use controllers\databaseController;

// class passController
// {
//     private databaseController $db;
//     private playerComponent $playerComponent;

//     public function __construct($db)
//     {
//         $this->db = $db;
//     }

//     public function pass()
//     {
//         $args = [
//             'type' => 'pass',
//             'gameID' => $_SESSION['game_id'],
//             'previousMove' => $_SESSION['last_move'],
//         ];

//         $lastMove = $this->db->makeMove($args);

//         if ($lastMove !== false) {
//             $_SESSION['last_move'] = $lastMove;
//             $this->playerComponent->playerSwitch();
//         } else {
//         }
//     }
// }
