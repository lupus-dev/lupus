<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni di un evento
 */
class Event {

    /**
     * Identificativo dell'evento
     * @var int
     */
    public $id_event;

    /**
     * Identificativo della partita
     * @var int
     */
    public $id_game;

    /**
     * Codice dell'evento
     * @var \EventCode
     */
    public $event_code;

    /**
     * Contenuto dell'evento
     * @var array
     */
    public $event_data;

    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }

    /**
     * Cerca un evento dall'identificativo
     * @param int $id Identificativo dell'evento da cercare
     * @return boolean|\Event Ritorna l'evento cercato. False se non trovato
     */
    public static function fromIdEvent($id) {
        $id = intval($id);

        $query = "SELECT id_event,id_game,event_code,event_data FROM event "
                . "WHERE id_event=$id";
        $res = Database::query($query);

        if (!$res || count($res) != 1) {
            logEvent("Evento con id_event=$id non trovato", LogLevel::Warning);
            return false;
        }

        $event = new Event();
        $event->id_event = $res[0]["id_event"];
        $event->id_game = $res[0]["id_game"];
        $event->event_code = $res[0]["event_code"];
        $event->event_data = json_decode($res[0]["event_data"], true);

        return $event;
    }

    /**
     * Cerca tutti gli eventi della partita
     * @param \Game $game Partita di cui cercare gli eventi
     * @return array Ritorna un vettore di \Event contentente tutti gli eventi
     * della partita
     */
    public static function getGameEvent($game) {
        $id_game = $game->id_game;

        $query = "SELECT id_event,id_game,event_code,event_data FROM event "
                . "WHERE id_game=$id_game";
        $res = Database::query($query);

        $events = array();
        foreach ($res as $e) {
            $event = new Event();
            $event->id_event = $e["id_event"];
            $event->id_game = $e["id_game"];
            $event->event_code = $e["event_code"];
            $event->event_data = json_decode($e["event_data"], true);
            
            $events[] = $event;
        }
        
        return $events;
    }

    /**
     * Aggiunge alla lista degli eventi l'inizio della partita
     * @param \Game $game Partita iniziata
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertGameStart($game) {
        $data = array(
            "players" => array(),
            "start" => time()
        );
        
        $users = $game->getUsers();
        foreach ($users as $user) {
            $data["players"][] = $user->username;
        }
        
        return Event::insertEvent($game, EventCode::GameStart, $data);
    }
    
    /**
     * Aggiunge alla lista degli eventi della partita la morte di un giocatore
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $dead Utente morto
     * @param string $cause Causa della morte
     * @param string $actor Causante della morte (killer)
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertDeath($game, $dead, $cause, $actor) {
        $data = array(
            "dead" => $dead->username,
            "cause" => $cause,
            "actor" => $actor
        );
        
        return Event::insertEvent($game, EventCode::Death, $data);
    }

    /**
     * Aggiunge alla lista degli eventi della partita la visione di un medium
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $medium Il medium attore della visione
     * @param \User $seen L'utente protagonista della visione
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertMediumAction($game, $medium, $seen) {
        $engine = new Engine($game);
        $role = Role::fromUser($seen, $engine);
        $role = get_class($role);
        $data = array(
            "medium" => $medium->username,
            "seen" => $seen->username,
            "mana" => $role::$mana
        );
        
        return Event::insertEvent($game, EventCode::MediumAction, $data);
    }
    /**
     * Aggiunge alla lista degli eventi della partita la visione di un veggente
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $veggente Il veggente attore della visione
     * @param \User $seen L'utente protagonista della visione
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertVeggenteAction($game, $veggente, $seen) {
        $engine = new Engine($game);
        $role = Role::fromUser($seen, $engine);
        $role = get_class($role);
        $data = array(
            "veggente" => $veggente->username,
            "seen" => $seen->username,
            "mana" => $role::$mana
        );
        
        return Event::insertEvent($game, EventCode::MediumAction, $data);
    }
    /**
     * Aggiunge alla lista degli eventi della partita le foto del paparazzo
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $paparazzo Il paparazzo autore delle foto
     * @param \User $seen L'utente protagonista delle foto
     * @param array $visitors Elenco dei giocatori che sono stati visti con l'utente
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertPaparazzoAction($game, $paparazzo, $seen, $visitors) {
        $data = array(
            "paparazzo" => $paparazzo->username,
            "seen" => $seen->username,
            "visitors" => $visitors
        );
        
        return Event::insertEvent($game, EventCode::MediumAction, $data);
    }
    /**
     * Inserisce un evento nel database
     * @param \Game $game Partita in cui inserire l'evento
     * @param \EventCode $event_code Codice dell'evento da inserire
     * @param array $event_data Informazioni dell'evento
     * @return boolean|\Event Ritorna l'evento creato. False se si verifica un 
     * errore
     */
    private static function insertEvent($game, $event_code, $event_data) {
        $id_game = $game->id_game;        
        $event_data = Database::escape(json_encode($event_data));
        
        $query = "INSERT INTO event (id_game,event_code,event_data) VALUE "
                . "($id_game,$event_code,'$event_data')";
        $res = Database::query($query);
        if (!$res) {
            logEvent("Impossibile inserire l'evento id_game=$id_game event_code=$event_code", LogLevel::Warning);
            return false;
        }
        
        $id_event = Database::$mysqli->insert_id;
        if (!$id_event) {
            logEvent("Impossibile recuperare l'evento creato", LogLevel::Warning);
            return false;
        }
        
        return Event::fromIdEvent($id_event);
    }
    
}
