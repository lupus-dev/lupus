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
    public $players;

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

        $query = "SELECT id_game,id_room,day,status,game_name,game_descr,players FROM game WHERE id_game=$id";
        $res = Database::query($query);

        if (count($res) != 1) {
            logEvent("Partita non trovata. id_game=$id", LogLevel::Warning);
            return false;
        }
        $game = new Game();
        $game->id_game = $res[0]["id_game"];
        $game->id_room = $res[0]["id_room"];
        $game->day = $res[0]["day"];
        $game->status = $res[0]["status"];
        $game->game_name = $res[0]["game_name"];
        $game->game_descr = $res[0]["game_descr"];
        if (intval($res[0]["players"]) > 0)
            $game->players = $res[0]["players"];
        else
            $game->players = json_decode($res[0]["players"], true);

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
        if (!$room) {
            logEvent("Stanza non trovata. room_name=$room", LogLevel::Warning);
            return false;
        }
        $id_room = $room->id_room;

        $query = "SELECT id_game,id_room,day,status,game_name,game_descr,players FROM game WHERE id_room=$id_room AND game_name='$game'";
        $res = Database::query($query);

        if (count($res) != 1) {
            logEvent("Partita non trovata. room_name={$room->room_name} game_name=$game", LogLevel::Warning);
            return false;
        }
        $game = new Game();
        $game->id_game = $res[0]["id_game"];
        $game->id_room = $res[0]["id_room"];
        $game->day = $res[0]["day"];
        $game->status = $res[0]["status"];
        $game->game_name = $res[0]["game_name"];
        $game->game_descr = $res[0]["game_descr"];
        if (intval($res[0]["players"]) > 0)
            $game->players = $res[0]["players"];
        else
            $game->players = json_decode($res[0]["players"], true);

        return $game;
    }

    /**
     * Genera la risposta da dare come informazioni nelle API
     * @param \Game $game Partita da ritornare
     * @return array Vettore contenente le informazioni della partita
     */
    public static function makeResponse($game) {
        $room = Room::fromIdRoom($game->id_room);
        $res = array(
            "room_name" => $room->room_name,
            "game_name" => $game->game_name,
            "day" => array(
                "num_day" => (int) $game->day,
                "game_time" => GameTime::getNameFromDay($game->day),
                "game_time_num" => (int) ($game->day / 2) + 1
            ),
            "status" => (int) $game->status,
            "game_descr" => $game->game_descr,
            "num_players" => $game->players["num_players"],
            "registred_players" => count($game->players["players"])
        );
        return $res;
    }

    /**
     * Crea una nuova partita. Non vengono effettuati controlli.
     * @param string $room Nome della stanza
     * @param string $name Nome della partita
     * @param string $descr Descrizione della partita
     * @param int $num_players Numero di giocatori nella partita
     * @return boolean|\Game Ritorna la partita creata. False se si verifica un
     * errore
     */
    public static function createGame($room, $name, $descr, $num_players) {
        $room = Room::fromRoomName($room);
        if (!$room) {
            logEvent("Stanza non trovata. room_name=$room", LogLevel::Warning);
            return false;
        }
        $admin = User::fromIdUser($room->id_admin);
        if (!$admin) {
            logEvent("Amministratore della stanza non trovato. id_admin={$room->id_admin}", LogLevel::Warning);
            return false;
        }
        $name = Database::escape($name);
        $descr = Database::escape($descr);
        $players = json_encode(array(
            "num_players" => $num_players,
            "players" => array($admin->username)
        ));
        $players = Database::escape($players);

        $id_room = $room->id_room;

        $query = "INSERT INTO game (id_room,day,status,game_name,game_descr,players) VALUE "
                . "($id_room,0,0,'$name','$descr','$players')";

        $res = Database::query($query);
        if (!$res)
            return false;
        return Game::fromRoomGameName($room->room_name, $name);
    }

    /**
     * Cerca tutti gli utenti della partita
     * @return array Vettore di \User
     */
    public function getUsers() {
        $id_game = $this->id_game;

        $query = "SELECT id_user FROM role WHERE id_game=$id_game";
        $res = Database::query($query);

        $users = array();

        foreach ($res as $id_user) {
            $user = User::fromIdUser($id_user["id_user"]);
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Imposta lo stato della partita
     * @param \GameStatus $status Il nuovo stato
     * @return boolean True se la modifica ha avuto successo, false altrimenti
     */
    public function status($status) {
        $status = intval($status);
        if ($status == 0) {
            logEvent("Stato $status non valido. Partita terminata", LogLevel::Warning);
            $status = GameStatus::TermByBug;
        }
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

    /**
     * Verifica se un utente appartiene alla partita
     * @param int $id_user Identificativo dell'utente
     * @return boolean rue se l'utente appartiene alla partita. False altrimenti
     */
    public function inGame($id_user) {
        $user = User::fromIdUser($id_user);
        if (!$user)
            return false;
        return in_array($user->username, $this->players["players"]);
    }

    /**
     * Effettua le operazioni per far entrare il giocatore nella partita
     * @param \User $user Utente che deve entrare nella partita
     * @return boolean True se il giocatore è entrato nella partita. False 
     * altrimenti
     */
    public function joinGame($user) {
        $id_game = $this->id_game;
        
        if ($this->inGame($user->id_user))
            return false;
        if ($this->status != GameStatus::NotStarted)
            return false;
        if (count($this->players["players"])+1 > $this->players["num_players"])
            return false;
        
        $this->players["players"][] = $user->username;
        
        $players = Database::escape(json_encode($this->players));
        
        $query = "UPDATE game SET players='$players' WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }

    /**
     * Fa avviare la partita, permette ai giocatori di entrare
     * @return boolean True se l'operazione ha avuto successo. False altrimenti
     */
    public function startGame() {
        if ($this->status != GameStatus::Setup)
            return false;
        logEvent("La partita {$this->game_name} è iniziata", LogLevel::Debug);
        Event::insertGameStart($this);
        $this->status(GameStatus::NotStarted);
        return true;
    }
    
    /**
     * Verifica se l'utente deve votare nella partita oppure è in attesa
     * @param \User $user Utente da verificare
     * @return boolean True se la partita è in attesa del voto dell'utente, false
     * altrimenti
     */
    public function hasToVote($user) {
        if (!$this->inGame($user->id_user))
            return false;
        $engine = new Engine($this);
        $role = Role::fromUser($user, $engine);
        switch (GameTime::fromDay($this->day)) {
            case GameTime::Start:
                return false;
            case GameTime::Day:
                return $role->needVoteDay();
            case GameTime::Night:
                return $role->needVoteNight();
        }
        return false;
    }
    /**
     * Modifica i parametri della partita
     * @param string $game_descr Descrizione della partita
     * @param int $num_players Numero di giocatori della partita
     * @return boolean True se l'operazione ha avuto successo, false altrimenti
     */
    public function editGame($game_descr, $num_players) {
        $this->game_descr = $game_descr;
        $this->players["num_players"] = $num_players;
        
        $game_descr = Database::escape($game_descr);
        $players = Database::escape(json_encode($this->players));
        $id_game = $this->id_game;
        
        $query = "UPDATE game SET game_descr='$game_descr', players='$players' WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }
    /**
     * Ottiene una lista dei giocatori vivi della partita
     * @return boolean|array Un vettore di \User. False se si verifica un errore
     */
    public function getAlive() {
        $id_game = $this->id_game;
        $alive = RoleStatus::Alive;
        
        $query = "SELECT id_user FROM role WHERE id_game=$id_game AND status=$alive";
        $res = Database::query($query);
        if (!$res)
            return false;
        
        $users = array();
        
        foreach ($res as $user) 
            $users[] = User::fromIdUser($user["id_user"]);
        
        return $users;
    }
    /**
     * Ottiene una lista dei giocatori morti della partita
     * @return boolean|array Un vettore di \User. False se si verifica un errore
     */
    public function getDead() {
        $id_game = $this->id_game;
        $dead = RoleStatus::Dead;
        
        $query = "SELECT id_user FROM role WHERE id_game=$id_game AND status=$dead";
        $res = Database::query($query);
        if (!$res)
            return false;
        
        $users = array();
        
        foreach ($res as $user) 
            $users[] = User::fromIdUser($user["id_user"]);
        
        return $users;
    }
}
