<?php

namespace components;

class gameComponent
{
    private boardComponent $board;
    private playerComponent $player;
    private $id;

    public function __construct($board, $player, $id)
    {
        $this->board = $board;
        $this->player = $player;
        $this->id = $id;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setBoard($board)
    {
        $this->board = $board;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }
}
