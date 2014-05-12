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
     * Giorno in cui è stato inserito l'evento
     * @var int
     */
    public $day;

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

        $query = "SELECT id_event,id_game,event_code,event_data,day FROM event "
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
        $event->day = $res[0]["day"];

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

        $query = "SELECT id_event,id_game,event_code,event_data,day FROM event "
                . "WHERE id_game=$id_game";
        $res = Database::query($query);

        $events = array();
        foreach ($res as $e) {
            $event = new Event();
            $event->id_event = $e["id_event"];
            $event->id_game = $e["id_game"];
            $event->event_code = $e["event_code"];
            $event->event_data = json_decode($e["event_data"], true);
            $event->day = $e["day"];

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

        return Event::insertEvent($game, EventCode::VeggenteAction, $data);
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
        $day = $game->day;

        $query = "INSERT INTO event (id_game,event_code,event_data,day) VALUE "
                . "($id_game,$event_code,'$event_data',$day)";
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

    /**
     * Ritorna l'evento formattato da mettere nel giornale
     * @param \Event $event Evento da formattare
     * @param \User $user Utente a cui appartiene il giornale
     * @return array|boolean False se la notizia non è visibile, un vettore con
     * le informazioni da postare altrimenti
     */
    public static function getNewsFromEvent($event, $user) {
        switch ($event->event_code) {
            case EventCode::GameStart:
                return Event::getNewsFromGameStart($event);
            case EventCode::Death:
                return Event::getNewsFromDeath($event, $user);
            case EventCode::MediumAction:
                return Event::getNewsFromMediumAction($event, $user);
            case EventCode::VeggenteAction:
                return Event::getNewsFromVeggenteAction($event, $user);
            case EventCode::ParapazzoAction:
                return Event::getNewsFromPaparazzoAction($event, $user);
            default:
                return false;
        }
    }

    /**
     * Formatta l'inizio della partita
     * @param \Event $event Evento dell'inizio della partita
     * @return array Ritorna le informazioni dell'inizio della partita
     */
    private static function getNewsFromGameStart($event) {
        $timestamp = $event->event_data["start"];
        $date = date("d-m-Y", $timestamp);
        $hour = date("H:i:s", $timestamp);
        return array(
            "day" => 0,
            "news" => "La partita è iniziata il $date alle $hour"
        );
    }

    /**
     * Formatta la morte di un giocatore
     * @param \Event $event Evento dell'inizio della partita
     * @param \User $user Utente che partecipa alla partita
     * @return array Ritorna le informazioni della morte
     */
    private static function getNewsFromDeath($event, $user) {
        $game = Game::fromIdGame($event->id_game);
        $username = $event->event_data["dead"];
        $killer = $event->event_data["actor"];

        $news = "";

        if ($event->event_data["cause"] == "kill-day")
            $news = "Il giocatore $username è stato messo al rogo";
        else
            $news = "Il giocatore $username è stato trovato morto";

        if ($game->status >= GameStatus::Winy) {
            switch ($event->event_data["cause"]) {
                case "kill-day":
                    $news = "Il giocatore $username è stato messo al rogo. Focolare preparato da $killer";
                    break;
                case "kill-assassino":
                    $news = "Il giocatore $username è stato assassinato da $killer";
                    break;
                case "kill-lupo":
                    $news = "Il giocatore $username è stato sbranato da $killer";
                    break;
                case "suicidio-pastore":
                    $news = "Il giocatore $username ha sacrificato una pecora di troppo";
                    break;
            }
        }
        return array(
            "day" => $event->day,
            "news" => $news
        );
    }

    /**
     * Formatta la visione di un medium
     * @param \Event $event Evento della visione
     * @param \User $user Utente che dovrebbe vedere l'evento
     * @return array|boolean Ritorna le informazioni della visione. False se non la può
     * vedere
     */
    private static function getNewsFromMediumAction($event, $user) {
        if ($event->event_data["medium"] != $user->username)
            return false;
        $mana = ($event->event_data["mana"] == Mana::Good) ? "buono" : "cattivo";
        $username = $event->event_data["seen"];

        return array(
            "day" => $event->day,
            "news" => "Il giocatore $username aveva un mana $mana"
        );
    }

    /**
     * Formatta la visione di un veggente
     * @param \Event $event Evento della visione
     * @param \User $user Utente che dovrebbe vedere l'evento
     * @return array|boolean Ritorna le informazioni della visione. False se non la può
     * vedere
     */
    private static function getNewsFromVeggenteAction($event, $user) {
        if ($event->event_data["veggente"] != $user->username)
            return false;
        $mana = ($event->event_data["mana"] == Mana::Good) ? "buono" : "cattivo";
        $username = $event->event_data["seen"];

        return array(
            "day" => $event->day,
            "news" => "Il giocatore $username ha un mana $mana"
        );
    }
    
    /**
     * Formatta le foto di un paparazzo
     * @param \Event $event Evento da formattare
     * @return array Ritorna le informazioni delle foto del paparazzo
     */
    private static function getNewsFromPaparazzoAction($event) {
        $game = Game::fromIdGame($event->id_game);
        $username = $event->event_data["seen"];
        
        $news = "Il giocatore $username è stato paparazzato ";
        $visitors = $event->event_data["visitors"];
        
        if (count($visitors) == 0)
            $news .= "da solo";
        else if (count($visitors) == 1)
            $news .= "insieme a " . $visitors[0];
        else {
            $chunk = array_chunk($visitors, count($visitors)-1);
            $news .= "insieme a " . implode(", ", $chunk[0]) . " e " . $chunk[1][0];
        }
        
        if ($game->status >= GameStatus::Winy)
            $news .= " da " . $event->event_data["paparazzo"];
        
        return array(
            "day" => $event->day,
            "news" => $news
        );
    }
}
