<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni di un utente
 */
class User {

    /**
     * Identificativo dell'utente
     * @var int
     */
    public $id_user;

    /**
     * Username dell'utente
     * @var string
     */
    public $username;

    /**
     * Livello dell'utente
     * @var int
     */
    public $level;

    /**
     * Il nome dell'utente
     * @var string
     */
    public $name;

    /**
     * Il cognome dell'utente
     * @var type 
     */
    public $surname;

    /**
     * Costruttore privato...
     */
    private function __construct() {
        
    }

    /**
     * Crea un'istanza di \User dal suo identificativo
     * @param int $id Identificativo dell'utente
     * @return boolean|\User L'utente con l'identificativo specificato. False
     * se non è stato trovato
     */
    public static function fromIdUser($id) {
        $id = intval($id);

        $query = "SELECT id_user,username,level,name,surname FROM user WHERE id_user=$id";
        $res = Database::query($query);

        if (count($res) != 1) {
            logEvent("L'utente $id non esiste", LogLevel::Warning);
            return false;
        }

        $user = new User();
        $user->id_user = $res[0]["id_user"];
        $user->username = $res[0]["username"];
        $user->level = $res[0]["level"];
        $user->name = $res[0]["name"];
        $user->surname = $res[0]["surname"];

        return $user;
    }

    /**
     * Crea un'istanza di \User dal suo username
     * @param string $username Username dell'utente
     * @return boolean|\User L'utente con lo username specificato. False se non 
     * è stato trovato
     */
    public static function fromUsername($username) {
        $username = Database::escape($username);

        $query = "SELECT id_user,username,level,name,surname FROM user WHERE username='$username'";
        $res = Database::query($query);

        if (count($res) != 1) {
            logEvent("L'utente $username non esiste", LogLevel::Warning);
            return false;
        }

        $user = new User();
        $user->id_user = $res[0]["id_user"];
        $user->username = $res[0]["username"];
        $user->level = $res[0]["level"];
        $user->name = $res[0]["name"];
        $user->surname = $res[0]["surname"];

        return $user;
    }

    /**
     * Controlla se la coppia username/password è corretta
     * @param string $username Nome utente
     * @param string $password Password
     * @return int|bool False se username/password sono errati, l'id dell'utente 
     * se sono corretti
     */
    public static function checkLogin($username, $password) {
        $username = Database::escape($username);
        $password = Database::escape($password);

        $query = "SELECT id_user FROM user WHERE username='$username' AND password=SHA1('$password')";
        $res = Database::query($query);
        return count($res) == 1 ? $res[0]["id_user"] : false;
    }

    /**
     * Ottiene la lista dei nomi delle stanze pubbliche associate all'utente
     * @return string Un vettore di room_name
     */
    public function getPublicRoom() {
        $id_admin = $this->id_user;
        $query = "SELECT room_name FROM room WHERE id_admin=$id_admin AND private=0";
        $res = Database::query($query);

        $rooms = array();
        foreach ($res as $room)
            $rooms[] = $room["room_name"];

        return $rooms;
    }

    /**
     * Ottiene la lista dei nomi delle stanze private associate all'utente
     * @return string Un vettore di room_name
     */
    public function getPrivateRoom() {
        $id_admin = $this->id_user;
        $query = "SELECT room_name FROM room WHERE id_admin=$id_admin AND private=1";
        $res = Database::query($query);

        $rooms = array();
        foreach ($res as $room)
            $rooms[] = $room["room_name"];

        return $rooms;
    }

    /**
     * Ottiene una lista delle partite attive dell'utente
     * @return array Ritorna un vettore di coppie. Ogni coppia contiene la chiave
     * room_name e la chiave game_name
     */
    public function getActiveGame() {
        $id_user = Database::escape($this->id_user);
        $notStarted = GameStatus::NotStarted;
        $running = GameStatus::Running;
        // ottiene il game_name e la room_name
        $query = "SELECT game_name,(SELECT room_name FROM room WHERE room.id_room=game.id_room) AS room_name "
                . "FROM game WHERE (status=$notStarted OR status=$running) AND "
                . "(SELECT COUNT(*) FROM player WHERE player.id_game=game.id_game AND id_user=$id_user)=1";
        $res = Database::query($query);

        $games = array();
        foreach ($res as $game)
            $games[] = $game;

        return $games;
    }

    /**
     * Ottiene una lista delle partite in fase di setup appartenenti all'utente
     * @return array Ritorna un vettore di coppie. Ogni coppia contiene la chiave
     * room_name e la chiave game_name
     */
    public function getSetupGame() {
        $id_user = $this->id_user;
        $setup = GameStatus::Setup;

        $query = "SELECT game_name,(SELECT room_name FROM room WHERE room.id_room=game.id_room) AS room_name "
                . "FROM game WHERE status=$setup AND (SELECT id_admin FROM room WHERE room.id_room=game.id_room)=$id_user";
        $res = Database::query($query);

        $games = array();
        foreach ($res as $game)
            $games[] = $game;

        return $games;
    }

    /**
     * Ottiene una lista delle partite in terminate appartenenti all'utente
     * @return array Ritorna un vettore di coppie. Ogni coppia contiene la chiave
     * room_name e la chiave game_name
     */
    public function getEndedGame() {
        $id_user = $this->id_user;
        $winy = GameStatus::Winy;
        // ottiene il game_name e la room_name
        $query = "SELECT game_name,(SELECT room_name FROM room WHERE room.id_room=game.id_room) AS room_name "
                . "FROM game WHERE status>=$winy AND "
                . "(SELECT COUNT(*) FROM player WHERE player.id_game=game.id_game AND id_user=$id_user)=1";
        $res = Database::query($query);

        $games = array();
        foreach ($res as $game)
            $games[] = $game;

        return $games;
    }

    /**
     * Verifica se l'utente può creare altre stanze
     * @return boolean True se l'utente può creare un'altra stanza. False altrimenti
     */
    public function canCreatePublicRoom() {
        $numPublicRooms = count($this->getPublicRoom());
        $numPrivateRooms = count($this->getPrivateRoom());
        $level = Level::getLevel($this->level);

        if ($numPublicRooms + $numPrivateRooms + 1 > $level->aviableRoom)
            return false;
        return true;
    }

    /**
     * Verifica se l'utente può creare altre stanze private
     * @return boolean True se l'utente può creare un'altra stanza privata. 
     * False altrimenti
     */
    public function canCreatePrivateRoom() {
        $numPrivateRooms = count($this->getPrivateRoom());
        $level = Level::getLevel($this->level);

        if ($numPrivateRooms + 1 > $level->privateRoom)
            return false;
        return true;
    }

    /**
     * Effettua la registrazione di un utente
     * @param string $username Nome utente
     * @param string $password Password
     * @param string $nome Nome
     * @param string $cognome Cognome
     * @return \User|boolean False se si verifica un errore. L'utente registrato
     * altrimenti
     */
    public static function signup($username, $password, $nome, $cognome) {
        $user = User::fromUsername($username);
        if ($user)
            return false;
        
        $username = $username;
        $password = Database::escape($password);
        $nome = Database::escape($nome);
        $cognome = Database::escape($cognome);
        
        $query = "INSERT INTO user (username,password,level,name,surname) VALUE "
                . "('$username', SHA1('$password'), 2, '$nome', '$cognome')";
        $res = Database::query($query);
        if (!$res)
            return false;
        return User::fromUsername($username);
    }
}
