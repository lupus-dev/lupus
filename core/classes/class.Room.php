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
     * Indica se la stanza è visibile al pubblico
     * @var boolean
     */
    public $private;

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

        $query = "SELECT id_room,id_admin,room_name,room_descr,private FROM room WHERE id_room=?";
        $res = Database::query($query, [$id]);

        if (count($res) != 1) {
            logEvent("La stanza $id non esiste", LogLevel::Debug);
            return false;
        }
            

        $room = new Room();
        $room->id_room = $res[0]["id_room"];
        $room->id_admin = $res[0]["id_admin"];
        $room->room_name = $res[0]["room_name"];
        $room->room_descr = $res[0]["room_descr"];
        $room->private = $res[0]["private"];

        return $room;
    }

    /**
     * Crea un'istanza di \Room dal suo identificativo
     * @param \string $name Nome breve della stanza
     * @return \Room|boolean La stanza con il nome speficifato. False
     * se non trovata
     */
    public static function fromRoomName($name) {
        $query = "SELECT id_room,id_admin,room_name,room_descr,private FROM room WHERE room_name=?";
        $res = Database::query($query, [$name]);

        if (count($res) != 1) {
            logEvent("La stanza $name non esiste", LogLevel::Debug);
            return false;
        }

        $room = new Room();
        $room->id_room = $res[0]["id_room"];
        $room->id_admin = $res[0]["id_admin"];
        $room->room_name = $res[0]["room_name"];
        $room->room_descr = $res[0]["room_descr"];
        $room->private = $res[0]["private"];

        return $room;
    }

    /**
     * Restituisce true se la stanza cercata esiste nel database
     * @param \string $name Nome breve della stanza
     * @return \boolean True se la stanza esiste, False altrimenti
     */
    public static function checkIfExists($name) {
        $query = "SELECT 1 FROM room WHERE room_name=?";
        $res = Database::query($query, [$name]);

        return count($res) == 1;
    }

    /**
     * Genera la risposta da dare come informazioni nelle API
     * @param \Room $room Stanza da ritornare
     * @return array Vettore contenente le informazioni della stanza
     */
    public static function makeResponse($room) {
        $admin = User::fromIdUser($room->id_admin);
        $res = array(
            "room_name" => $room->room_name,
            "room_descr" => $room->room_descr,
            "admin" => $admin->username,
            "private" => $room->private,
            "games" => $room->getGame()
        );
        return $res;
    }

    /**
     * Crea una nuova stanza. Non vengono effettuati controlli sui permessi 
     * dell'utente
     * @param string $name Nome breve della stanza
     * @param string $descr Descrizione della stanza
     * @param int $admin Identificativo dell'amministratore
     * @param boolean $private Indica se la stanza è privata
     * @return \Room|boolean Ritorna la stanza creata. False se si verifica un 
     * errore
     */
    public static function createRoom($name, $descr, $admin, $private) {
        $admin = intval($admin);
        $private = intval($private);

        $query = "INSERT INTO room (id_admin,room_name,room_descr,private) VALUE
                  (?, ?, ?, ?)";
        $res = Database::query($query, [$admin, $name, $descr, $private]);
        if (!$res)
            return false;
        return Room::fromRoomName($name);
    }

    /**
     * Cerca i nomi delle partite nella stanza
     * @return array Vettore dei game_name delle partite nella stanza
     */
    public function getGame() {
        $id_room = $this->id_room;
        $query = "SELECT game_name FROM game WHERE id_room=? ORDER BY id_game";
        $res = Database::query($query, [$id_room]);

        $games = array();
        foreach ($res as $game)
            $games[] = $game["game_name"];

        return $games;
    }
    
    /**
     * Controlla se in questa stanza tutte le partite sono terminate
     * @return boolean Return true se tutte le partite sono terminate, false
     * altrimenti
     */
    public function isAllTerminated() {
        $id_room = $this->id_room;
        
        $query = "SELECT COUNT(*) AS count FROM game WHERE id_room=? AND status<200";
        $res = Database::query($query, [$id_room]);
        
        if (count($res) != 1)
            return false;
        return $res[0]["count"] == 0;
    }

    /**
     * Controlla se un utente può accedere alla stanza.
     * Un utente può accedere alla stanza se:
     * - La stanza è aperta
     * - La stanza è link-only
     * - L'utente è nelle ACL della stanza
     * @param User $user Utente da controllare
     * @return bool True se l'utente può accedere, False altrimenti
     */
    public function checkAuthorized($user) {
        if ($this->private == RoomPrivate::Open) return true;
        if ($this->private == RoomPrivate::LinkOnly) return true;

        if ($this->id_admin == $user->id_user) return true;

        $sql = "SELECT * FROM room_acl WHERE id_room=? AND id_user=?";
        $res = Database::query($sql, [$this->id_room, $user->id_user]);

        if ($res && count($res) == 1) return true;

        logEvent("L'utente $user->username non può accedere alla stanza $this->room_name", LogLevel::Warning);

        return false;
    }

    /**
     * Ritorna una lista degli identificativi degli utenti che possono accedere a questa stanza
     * (se è del tipo ACL). L'admin della stanza è in questo elenco solo se $include_admin è true
     * @param boolean $include_admin Indica se includere l'admin nell'elenco
     * @return array|false Ritorna false se la stanza non è ACL altrimenti l'elenco degli utenti
     * autorizzati
     */
    public function getACLUsers($include_admin = true) {
        if ($this->private != RoomPrivate::ACL) return false;

        $users = array();
        if ($include_admin) $users[] = $this->id_admin;

        $sql = "SELECT id_user FROM room_acl WHERE id_room=?";
        $res = Database::query($sql, [$this->id_room]);

        if ($res === false) return false;

        foreach ($res as $u)
            $users[] = $u["id_user"];
        return $users;
    }

    /**
     * Aggiunge una regola all'ACL della stanza corrente. Non vengono effettuati controlli
     * @param User $user Utente da aggiungere all'ACL
     * @return bool True se l'inserimento ha avuto successo, false altrimenti
     */
    public function addACL($user) {
        $sql = "INSERT INTO room_acl (id_room, id_user) VALUES (?, ?)";
        $res = Database::query($sql, [$this->id_room, $user->id_user]);

        return !!$res;
    }

    /**
     * Rimuove un'ACL di una stanza, non vengono effettuati controlli
     * @param User $user Utente da rimuovere dall'ACL
     * @return bool True se l'operazione ha avuto successo, false altrimenti
     */
    public function removeACL($user) {
        $sql = "DELETE FROM room_acl WHERE id_room=? AND id_user=?";
        $res = Database::query($sql, [$this->id_room, $user->id_user]);

        return !!$res;
    }
}
