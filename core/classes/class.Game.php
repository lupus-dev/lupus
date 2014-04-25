<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni di una partita
 */
class Game {

    /**
     * Identificativo della partita
     * @var int
     */
    public $id_game;

    /**
     * Identificativo della stanza
     * @var int
     */
    public $id_room;

    /**
     * Numero del giorno nella partita
     * @var int
     */
    public $day;

    /**
     * Stato della partita
     * @var int
     */
    public $status;

    /**
     * Nome breve della partita
     * @var string
     */
    public $game_name;

    /**
     * Descrizione della partita
     * @var string
     */
    public $game_descr;

    /**
     * Lista dei ruoli registrati nella partita
     * @var array
     */
    public $roles;

    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }

    /**
     * Crea un'istanza di \Game dal suo identificativo
     * @param int $id Identificativo della partita
     * @return boolean|\Game La partita con l'identitificativo speficifato. False
     * se non trovata
     */
    public static function fromIdGame($id) {
        $id = intval($id);

        $query = "SELECT id_game,id_room,day,status,game_name,game_descr,roles FROM game WHERE id_game=$id";
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
        $game->roles = json_decode($res[0]["roles"], true);

        return $game;
    }

    /**
     * Crea un'istanza di \Game dal suo nome breve e dal nome della stanza
     * @param string $room Nome breve della stanza
     * @param string $game Nome breve della partita
     * @return boolean|\Game La partita. False se non trovata o se la stanza non
     * è valida
     */
    public static function fromRoomGameName($room, $game) {
        $game = Database::escape($game);

        $room = Room::fromRoomName($room);
        if (!$room)
            return false;
        $id_room = $room->id_room;

        $query = "SELECT id_game,id_room,day,status,game_name,game_descr,roles FROM game WHERE id_room=$id_room AND game_name='$game'";
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
        $game->roles = json_decode($res[0]["roles"], true);

        return $game;
    }

    /**
     * Genera la risposta da dare come informazioni nelle API
     * @param \Game $game Partita da ritornare
     * @param \User $user Utente che effettua la richiesta
     * @return array Vettore contenente le informazioni della partita
     */
    public static function makeResponse($game, $user) {
        $room = Room::fromIdRoom($game->id_room);
        $res = array(
            "room_name" => $room->room_name,
            "game_name" => $game->game_name,
            "day" => (int) $game->day,
            "status" => (int) $game->status,
            "game_descr" => $game->game_descr
        );
        if ($room->id_admin == $user->id_user)
            $res["roles"] = $game->roles;
        return $res;
    }
    
    /**
     * Crea una nuova partita. Non vengono effettuati controlli.
     * @param string $room Nome della stanza
     * @param string $name Nome della partita
     * @param string $descr Descrizione della partita
     * @param array $roles Elenco dei ruoli registrati nella partita
     * @return boolean|\Game Ritorna la partita creata. False se si verifica un
     * errore
     */
    public static function createGame($room, $name, $descr, $roles) {
        $room = Room::fromRoomName($room);
        if (!$room)
            return false;
        
        $name = Database::escape($name);
        $descr = Database::escape($descr);
        $roles = Database::escape(json_encode($roles));

        $id_room = $room->id_room;
        
        $query = "INSERT INTO game (id_room,day,status,game_name,game_descr,roles) VALUE "
                . "($id_room,0,0,'$name','$descr','$roles')";
        
        $res = Database::query($query);
        if (!$res)
            return false;
        return Game::fromRoomGameName($room->room_name, $name);
    }

    /**
     * Imposta lo stato della partita
     * @param \GameStatus $status Il nuovo stato
     * @return boolean True se la modifica ha avuto successo, false altrimenti
     */
    public function status($status) {
        $status = intval($status);
        if ($status == 0)
            $status = GameStatus::TermByBug;
        $id_game = $this->id_game;
        $query = "UPDATE game SET status=$status WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        $this->status = $status;
        return true;
    }    
    /**
     * Fa avanzare il giorno di uno
     * @return boolean True se l'azione ha avuto successo, false altrimenti
     */
    public function nextDay() {
        $id_game = $this->id_game;
        $query = "UPDATE game SET day=day+1 WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        $this->day++;
        return true;
    }
    
}
