<?php

// namespace controllers;

// use components\boardComponent;
// use controllers\databaseController;

// class playController
// {
//     private boardComponent $board;
//     private $player;
//     private $piece;
//     private $to;
//     private $hand;
//     private databaseController $db;


//     public function __construct($board, $piece, $to, databaseController $db)
//     {
//         $this->board = $board;
//         $this->player = $_SESSION['player'];
//         $this->piece = $piece;
//         $this->to = $to;
//         $this->hand = $_SESSION['hand'][$this->player];
//         $this->db = $db;
//     }

//     public function play()
//     {
//         unset($_SESSION['error']);
//         $board = $this->board->getBoard();

//         if (!$this->hand[$this->piece]) {
//             $_SESSION['error'] = "Player does not have tile"; // Check if the player has the selected tile.
//         } elseif (isset($board[$this->to])) {
//             $_SESSION['error'] = 'Board position is not empty'; // Check if the destination position is empty.
//         } elseif (count($board) && !$this->board->hasNeighBour($this->to)) {
//             $_SESSION['error'] = "board position has no neighbor"; // Check if the destination position has no neighboring tiles.
//         } elseif (array_sum($this->hand) < 11 && !$this->board->neighboursAreSameColor($this->player, $this->to)) {
//             $_SESSION['error'] = "Board position has opposing neighbor"; // Check if there are opposing color neighboring tiles.
//         } elseif ($this->piece != 'Q' && array_sum($this->hand) <= 8 && $this->hand['Q']) {
//             $_SESSION['error'] = 'Must play queen bee'; // Check if the player must play the queen bee tile.
//         } else {
//             $_SESSION['error'] = null;
//             // Place the selected piece on the board and update the player's hand.
//             $board[$this->to] = [[$this->player, $this->piece]];
//             $this->hand[$this->piece]--;
//             $_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.

//             $args = [
//                 'type' => 'play',
//                 'piece' => $this->piece,
//                 'gameID' => $_SESSION['game_id'],
//                 'to' => $this->to,
//                 'previousMove' => $_SESSION['last_move'],
//             ];

//             $lastMove = $this->db->makeMove($args);

//             if ($lastMove !== false) {
//                 $_SESSION['last_move'] = $lastMove;
//             } else {
//             }
//         }

//         $_SESSION['board'] = $board;
//         $_SESSION['hand'][$this->player] = $this->hand;
//     }
// }
