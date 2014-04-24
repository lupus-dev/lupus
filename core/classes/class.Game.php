<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class Game {
    public $id_game;
    public $id_room;
    public $day;
    public $status;
    public $game_name;
    public $game_descr;
    
    private function __construct() {
        
    }
    
    public static function fromIdGame($id) {
        $id = intval($id);

        $query = "SELECT id_game,id_room,day,status,game_name,game_descr FROM game WHERE id_game=$id";
        $res = Database::query($query);

        if (count($res) != 1)
            return false;

        $game = new Game();
        $game->id_game = $res[0]["id_game"];
        $game->id_room = $res[0]["id_room"];
        $game->day = $res[0]["day"];
        $game->status = $res[0]["status"];
        $game->game_name = $res[0]["game_name"];
        $game->game_descr = $res[0]["game_descr"];
        
        return $game;
    }
    
    public static function fromRoomGameName($room, $game) {
        $game = Database::escape($game);
        
        $room = Room::fromRoomName($room);
        if (!$room)
            return false;
        $id_room = $room->id_room;        
        
        $query = "SELECT id_game,id_room,day,status,game_name,game_descr FROM game WHERE id_room=$id_room AND game_name='$game'";
        $res = Database::query($query);

        if (count($res) != 1)
            return false;

        $game = new Game();
        $game->id_game = $res[0]["id_game"];
        $game->id_room = $res[0]["id_room"];
        $game->day = $res[0]["day"];
        $game->status = $res[0]["status"];
        $game->game_name = $res[0]["game_name"];
        $game->game_descr = $res[0]["game_descr"];
        
        return $game;
    }
}