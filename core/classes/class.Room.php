<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni di una stanza
 */
class Room {

    /**
     * Identificativo della stanza
     * @var int
     */
    public $id_room;

    /**
     * Identificativo dell'amministratore
     * @var int
     */
    public $id_admin;

    /**
     * Nome breve della stanza
     * @var string
     */
    public $room_name;

    /**
     * Nome lungo della stanza
     * @var string
     */
    public $room_descr;

    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }
    /**
     * Crea un'istanza di \Room dal suo identificativo
     * @param int $id Identitificativo della stanza
     * @return \Room|boolean La stanza con l'identitificativo speficifato. False
     * se non trovata
     */
    public static function fromIdRoom($id) {
        $id = intval($id);

        $query = "SELECT id_room,id_admin,room_name,room_descr FROM room WHERE id_room=$id";
        $res = Database::query($query);

        if (count($res) != 1)
            return false;

        $room = new Room();
        $room->id_room = $res[0]["id_room"];
        $room->id_admin = $res[0]["id_admin"];
        $room->room_name = $res[0]["room_name"];
        $room->room_descr = $res[0]["room_descr"];

        return $room;
    }
    /**
     * Crea un'istanza di \Room dal suo identificativo
     * @param int $id Identitificativo della stanza
     * @return \Room|boolean La stanza con l'identitificativo speficifato. False
     * se non trovata
     */
    public static function fromRoomName($name) {
        $name = Database::escape($name);

        $query = "SELECT id_room,id_admin,room_name,room_descr FROM room WHERE room_name='$name'";
        $res = Database::query($query);

        if (count($res) != 1)
            return false;

        $room = new Room();
        $room->id_room = $res[0]["id_room"];
        $room->id_admin = $res[0]["id_admin"];
        $room->room_name = $res[0]["room_name"];
        $room->room_descr = $res[0]["room_descr"];

        return $room;
    }
    
    /**
     * Cerca i nomi delle partite nella stanza
     * @return array Vettore dei game_name delle partite nella stanza
     */
    public function getGame() {
        $id_room = $this->id_room;
        $query = "SELECT game_name FROM game WHERE id_room=$id_room";
        $res = Database::query($query);
        
        $games = array();
        foreach ($res[0] as $game_name)
            $games[] = $game_name;
        
        return $games;
    }
}
