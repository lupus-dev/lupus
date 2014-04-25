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
}
