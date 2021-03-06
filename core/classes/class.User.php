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
     * Karma dell'utente
     * @var int
     */
    public $karma;

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

        $query = "SELECT id_user,username,level,karma,name,surname FROM user WHERE id_user=?";
        $res = Database::query($query, [$id]);

        if (count($res) != 1) {
            logEvent("L'utente $id non esiste", LogLevel::Warning);
            return false;
        }

        $user = new User();
        $user->id_user = $res[0]["id_user"];
        $user->username = $res[0]["username"];
        $user->level = $res[0]["level"];
        $user->karma = $res[0]["karma"];
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
        $query = "SELECT id_user,username,level,karma,name,surname FROM user WHERE username=?";
        $res = Database::query($query, [$username]);

        if (count($res) != 1) {
            logEvent("L'utente $username non esiste", LogLevel::Warning);
            return false;
        }

        $user = new User();
        $user->id_user = $res[0]["id_user"];
        $user->username = $res[0]["username"];
        $user->level = $res[0]["level"];
        $user->karma = $res[0]["karma"];
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
        $query = "SELECT id_user,password FROM user WHERE username=?";
        $res = Database::query($query, [$username]);

        if (count($res) == 1 && self::password_verify($password, $res[0]["password"]))
            return $res[0]["id_user"];
        return false;
    }

    /**
     * Ottiene la lista dei nomi delle stanze pubbliche associate all'utente
     * @return string Un vettore di room_name
     */
    public function getPublicRoom() {
        $id_admin = $this->id_user;
        $query = "SELECT room_name FROM room WHERE id_admin=? AND private=0";
        $res = Database::query($query, [$id_admin]);

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
        $query = "SELECT room_name FROM room WHERE id_admin=? AND private=1";
        $res = Database::query($query, [$id_admin]);

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
        $id_user = $this->id_user;
        $notStarted = GameStatus::NotStarted;
        $running = GameStatus::Running;
        // ottiene il game_name e la room_name
        $query = "SELECT game_name, room_name
                  FROM game
                  JOIN room ON room.id_room = game.id_room
                  JOIN player ON player.id_game = game.id_game
                  WHERE (game.status=? OR game.status=?) AND id_user=?";
        $res = Database::query($query, [$notStarted, $running, $id_user]);

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

        $query = "SELECT game_name, room_name
                  FROM game
                  JOIN room ON room.id_room = game.id_room
                  WHERE game.status=? AND id_admin=?";
        $res = Database::query($query, [$setup, $id_user]);

        $games = array();
        foreach ($res as $game)
            $games[] = $game;

        return $games;
    }

    /**
     * Ottiene una lista delle partite in terminate appartenenti all'utente
     * @param bool $includePrivate Indica se includere anche le partite private
     * @return array Ritorna un vettore di coppie. Ogni coppia contiene la chiave
     * room_name e la chiave game_name
     */
    public function getEndedGame($includePrivate = false) {
        $id_user = $this->id_user;
        $winy = GameStatus::Winy;
        // ottiene il game_name e la room_name
        $query = "SELECT game_name, room_name
                  FROM game
                  JOIN room ON room.id_room = game.id_room
                  JOIN player ON player.id_game = game.id_game
                  WHERE game.status>=? AND id_user=? AND room.private<=?";
        $res = Database::query($query, [$winy, $id_user, $includePrivate ? 10 : 0]);

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
     * Add (or remove) karma to the user
     * @param int $delta Karma to add or remove to the user
     * @return boolean True se l'operazione ha success, false altrimenti
     */
    public function addKarma($delta) {
        $sql = "UPDATE user SET karma = karma + ? WHERE id_user = ?";
        $res = Database::query($sql, [$delta, $this->id_user]);
        if (!$res) return false;

        $this->karma += $delta;
        logEvent(($delta >= 0 ? "+$delta" : "$delta") . " di karma per $this->username", LogLevel::Debug);

        Level::checkLevelAdvance($this);

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
        
        $password = self::password_hash($password);

        $query = "INSERT INTO user (username,password,level,karma,name,surname) VALUE 
                  (?, ?, 1, 0, ?, ?)";
        $res = Database::query($query, [$username, $password, $nome, $cognome]);
        if (!$res)
            return false;
        return User::fromUsername($username);
    }

    /**
     * Cifra una password con password_hash se PHP la supporta, altrimenti viene usato SHA1
     * per retrocompatibilità.
     * @param string $password Password da hashare
     * @return bool|string Hash della password
     */
    private static function password_hash($password) {
        if (function_exists("password_hash"))
            return password_hash($password, PASSWORD_BCRYPT);
        else
            return sha1($password);
    }

    /**
     * Verifica se la password inserita è corretta rispetto ad un hash nel database
     * @param string $password Password in chiaro da verificare
     * @param string $hash Hash della password, se è di 40 caratteri viene usata una versione NON SICURA
     * di SHA1, se di 60 e PHP supporta password_verify verrà usata una funzione sicura
     * @return bool True se le password corrispondono, false altrimenti
     * @throws Error Un errore verrà restituito se PHP non supporta l'hash
     */
    private static function password_verify($password, $hash) {
        if (strlen($hash) == 40)
            return sha1($password) == $hash;
        else if (strlen($hash) == 60)
            if (function_exists("password_verify"))
                return password_verify($password, $hash);
            else
                throw new Error("Too old version of PHP or password_verify not supported");
        else
            throw new Error("Password not recognized");
    }
}
