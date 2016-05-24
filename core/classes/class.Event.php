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

        $query = "SELECT id_event,id_game,event_code,event_data,day 
                  FROM event
                  WHERE id_event=?";
        $res = Database::query($query, [$id]);

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

        $query = "SELECT id_event,id_game,event_code,event_data,day 
                  FROM event
                  WHERE id_game=?";
        $res = Database::query($query, [$id_game]);

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

        $data["players"] = $game->getPlayers();

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

        return Event::insertEvent($game, EventCode::PaparazzoAction, $data);
    }

    /**
     * Inserisce l'evento della risurrezione di un utente da parte di un becchino
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $becchino Becchino che ha risorto l'utente
     * @param \User $dead Utente risorto
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertBecchinoAction($game, $becchino, $dead) {
        $data = array(
            "becchino" => $becchino->username,
            "dead" => $dead->username
        );
        
        return Event::insertEvent($game, EventCode::BecchinoAction, $data);
    }

    /**
     * Inserisce l'evento di un giocaotre espulso
     * @param \Game $game Partita in cui salvare l'evento
     * @param \User $kicked Giocatore espulso
     * @return boolean|\Event Evento creato. False se si verifica un errore
     */
    public static function insertPlayerKicked($game, $kicked) {
        $data = array(
            "kicked" => $kicked->username
        );

        return Event::insertEvent($game, EventCode::PlayerKicked, $data);
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
        $event_data = json_encode($event_data);
        $day = $game->day;

        $query = "INSERT INTO event (id_game,event_code,event_data,day) VALUE 
                  (?, ?, ?, ?)";
        $res = Database::query($query, [$id_game, $event_code, $event_data, $day]);
        if (!$res) {
            logEvent("Impossibile inserire l'evento id_game=$id_game event_code=$event_code", LogLevel::Warning);
            return false;
        }

        $id_event = Database::lastInsertId();
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
                return Event::getNewsFromDeath($event);
            case EventCode::MediumAction:
                return Event::getNewsFromMediumAction($event, $user);
            case EventCode::VeggenteAction:
                return Event::getNewsFromVeggenteAction($event, $user);
            case EventCode::PaparazzoAction:
                return Event::getNewsFromPaparazzoAction($event, $user);
            case EventCode::BecchinoAction:
                return Event::getNewsFromBecchinoAction($event);
            case EventCode::PlayerKicked:
                return Event::getNewsFromPlayerKicked($event);
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
        $game = Game::fromIdGame($event->id_game);

        $timestamp = $event->event_data["start"];
        $date = date("d/m/Y", $timestamp);
        $hour = date("H:i:s", $timestamp);
        $news = Event::getEventTemplate("GameStart", get_defined_vars());

        return array(
            "day" => 0,
            "news" => $news
        );
    }

    /**
     * Formatta la morte di un giocatore
     * @param \Event $event Evento dell'inizio della partita
     * @return array Ritorna le informazioni della morte
     */
    private static function getNewsFromDeath($event) {
        $game = Game::fromIdGame($event->id_game);
        $username = $event->event_data["dead"];
        $killer = $event->event_data["actor"];

        $news = Event::getEventTemplate("Death", get_defined_vars());

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

        $news = Event::getEventTemplate("Medium", get_defined_vars());

        return array(
            "day" => $event->day,
            "news" => $news
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

        $news = Event::getEventTemplate("Veggente", get_defined_vars());

        return array(
            "day" => $event->day,
            "news" => $news
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
        $visitors = $event->event_data["visitors"];

        $news = Event::getEventTemplate("Paparazzo", get_defined_vars());

        return array(
            "day" => $event->day,
            "news" => $news
        );
    }

    /**
     * Formatta la resurrezione di un becchino
     * @param \Event $event Evento da formattare
     * @param \User $user Utente che dovrebbe vedere l'evento
     * @return array|boolean Ritorna le informazioni della resurrezione. False se non la può
     * vedere
     */
    private static function getNewsFromBecchinoAction($event) {
        $game = Game::fromIdGame($event->id_game);
        $becchino = $event->event_data["becchino"];
        $dead = $event->event_data["dead"];

        $news = Event::getEventTemplate("Becchino", get_defined_vars());

        return array(
            "day" => $event->day,
            "news" => $news
        );
    }

    /**
     * Formatta l'espulsione di un giocatore
     * @param \Event $event Evento da formattare
     * @return array|boolean Ritorna le informazioni dell'espulsione.
     */
    private static function getNewsFromPlayerKicked($event) {
        $kicked = $event->event_data["kicked"];

        $news = Event::getEventTemplate("PlayerKicked", get_defined_vars());

        return array(
            "day" => $event->day,
            "news" => $news
        );
    }

    /**
     * Esegue un template e ritorna il risultato
     * @param string $file Percorso completo del template da usare
     * @param array $defined_vars Vettore associativo con le variabili definite (usare get_defined_vars)
     * @return string Output del template
     */
    private static function getEventTemplate($file, $defined_vars = array()) {
        extract($defined_vars);
        ob_start();
        include __DIR__ . "/../events/event.$file.php";
        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}
