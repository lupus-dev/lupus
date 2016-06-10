<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe astratta da cui devono derivare tutti i ruoli
 */
abstract class Role {

    // ----------- CARATTERISTICHE DEL RUOLO -------------
    
    /**
     * Nome breve del ruolo. Identifica il ruolo. La definizione del ruolo
     * è nel percorso: roles/role.Xxxx.php e il ruolo è una classe di nome Xxxx
     * con Xxxx il role_name con l'iniziale maiuscola
     * @var string
     */
    public static $role_name = "";

    /**
     * Nome completo del ruolo
     * @var string
     */
    public static $name = "";

    /**
     * Indica se questo ruolo è accessibile solo agli utenti con le funzioni di
     * debug sbloccate
     * @var boolean
     */
    public static $debug = false;

    /**
     * Indica se questo ruolo è abilitato
     * @var boolean
     */
    public static $enabled = true;

    /**
     * Priorità di esecuzione delle operazioni dei vari ruoli. Valori inferiori
     * hanno priorità maggiori
     * @var int
     */
    public static $priority = 1000;

    /**
     * Squadra di appartenenza del ruolo
     * @var \RoleTeam
     */
    public static $team_name;

    /**
     * Tipo di mana del ruolo
     * @var \Mana
     */
    public static $mana = Mana::Good;
    
    /**
     * Lista delle chat a cui l'utente può accedere
     * @var array
     */
    public static $chat_groups = array(ChatGroup::Game);

    /**
     * Probabilità che il ruolo venga scelto durante l'assegnazione dei ruoli
     * all'inizio della partita. Non è necessario che sia minore di 1
     * @var float
     */
    public static $gen_probability = 0;
    /**
     * Numero di ruoli uguali generati. Se dovessero venire generati più ruoli 
     * del dovuto, quelli attuali verrebbero scartati
     * @var int
     */
    public static $gen_number = 1;

    // ------------ INFORMAZIONI SULLA PARTITA ------------
    
    /**
     * Utente a cui appartiene il ruolo
     * @var \User
     */
    public $user;

    /**
     * Moteore della partita che gestisce il ruolo
     * @var \Engine
     */
    protected $engine;

    /**
     * Costruttore di \Role
     * @param \User $user Utente a cui appartiene il ruolo
     * @param \Engine $engine Motore della partita che gestisce il ruolo
     */
    public function __construct($user, $engine) {
        $this->user = $user;
        $this->engine = $engine;
    }

    /**
     * Questa funzione deve mostrare all'utente il proprio ruolo
     * @return string Un messaggio testuale che verrà incluso nella pagina 
     * nella zona per visualizzare il proprio ruolo
     */
    public abstract function splash();

    // ------------ SEZIONE NOTTE --------------

    /**
     * Questa funzione deve ritornare un valore booleano che indica se la partita
     * è in attesa del voto di questo personaggio in questa notte
     * 
     * Questa funzione viene chiamata prima che le azioni inizino ad essere 
     * applicate. Può essere quindi sfruttata per applicare delle azioni 
     * perventive (ad esempio proteggere). La funzione viene chiamata ad ogni
     * richiesta dell'utente... non appesantire il server...
     * @return boolean|array False se la partita può continuare, altrimenti
     * contiene vettore di \User contenente gli utenti votabili
     */
    public function needVoteNight() {
        return false;
    }

    /**
     * Effettua l'operazione associata al ruolo durante la notte
     * @return boolean Ritorna true se l'operazione è stata eseguita con successo.
     * False se si è verificato un errore. Un errore potrebbe interrompere l'intera
     * partita (codice 303).
     */
    public function performActionNight() {
        return true;
    }

    /**
     * Verifica se l'utente votato è valido per il personaggio durante la notte
     * @param string $username Username o 'flag' dell'utente votato
     * @return boolean Ritorna true se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        $user = User::fromUsername($username);
        if (!$user)
            return false;
        $status = Role::getRoleStatus($this->engine->game, $user->id_user);
        if ($status == RoleStatus::Dead)
            return false;
        return $username != $this->user->username;
    }

    // ------------ SEZIONE GIORNO --------------

    /**
     * Questa funzione deve ritornare un valore booleano che indica se la partita
     * è in attesa del voto di questo personaggio in questa giorno. Può essere 
     * modificata per adattarsi alle specifiche di personaggi che aviscono di 
     * giorno. E' implementata la funzione di un normale personaggio che vota 
     * chi mettere al rogo.
     * 
     * Questa funzione viene chiamata prima che le azioni inizino ad essere 
     * applicate. Può essere quindi sfruttata per applicare delle azioni 
     * perventive (ad esempio proteggere). La funzione viene chiamata ad ogni
     * richiesta dell'utente... non appesantire il server...
     * @return boolean|array False se la partita può continuare, altrimenti
     * contiene vettore associativo. L'indice votable continene un vettore di 
     * username degli utenti votabili. L'indice pre è una stringa html con delle 
     * informazioni da inserire prima del menu di selezione del voto
     */
    public function needVoteDay() {
        // un utente morto non vota
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $alive = $this->engine->game->getAlive();
            $votable = array();
            foreach ($alive as $user)
                if ($user != $this->user) 
                    $votable[] = $user->username;
            return array(
                "votable" => $votable,
                "pre" => "<p>Vota chi mettere al rogo come lupo!</p>"
            );
        }
        return false;
    }

    /**
     * Effettua l'operazione associata al ruolo durante il giorno. Se questo è
     * stato l'ultimo giocatore a votare allora sarà lui a compiere l'uccisione
     * del bersaglio se si sono verificate le condizioni opportune
     * @return boolean Ritorna true se l'operazione è stata eseguita con successo.
     * False se il giocatore da uccidere non esiste
     */
    public function performActionDay() {
        $votes = $this->getAllVoteDay();
        // se non è stato l'ultimo a votare, non fa nulla
        // esiste sempre almeno un voto. Altrimenti la partita sarebbe terminata
        if ($votes[0]["id_user"] != $this->user->id_user)
            return true;

        // TODO scegliere l'utente che mette al rogo tra coloro che hanno votato per la morte
        // dell'utente, non l'ultimo che ha votato
        logEvent("L'utente {$this->user->username} è stato scelto per mettere al rogo", LogLevel::Debug);

        $candidates = array();
        foreach ($votes as $vote)
            if (!isset($candidates[$vote["vote"]]))
                $candidates[$vote["vote"]] = 1;
            else
                $candidates[$vote["vote"]] ++;

        arsort($candidates);

        $num_votes = reset($candidates);
        $id_dead = key($candidates);
        $dead = User::fromIdUser($id_dead);

        // se il giocatore votato non esiste, c'è un bug nella votazione...
        if (!$dead) {
            logEvent("E' stato votato un giocatore inesistente ($id_dead x$num_votes)", LogLevel::Warning);
            return false;
        }
        // quorum
        if ($num_votes >= (int) (count($votes) * 0.5) + 1)
            if ($this->kill($dead)) {
                logEvent("Il giocatore {$dead->username} è stato messo al rogo", LogLevel::Debug);
                Event::insertDeath($this->engine->game, $dead, "kill-day", $this->user->username);
            } else
                logEvent("Il giocatore {$dead->username} non è stato messo al rogo", LogLevel::Debug);
        else
            logEvent("Non è stato raggiunto il quorum per la messa al rogo", LogLevel::Debug);
        return true;
    }

    /**
     * Cerca i voti di tutti quelli che hanno votato
     * @return boolean|array Ritorna un vettore con i voti e i votanti che 
     * durante questo giorno hanno votato. False se si verifica un errore
     */
    private function getAllVoteDay() {
        $id_game = $this->engine->game->id_game;
        $day = $this->engine->game->day;

        $query = "SELECT id_user,vote 
                  FROM vote 
                  WHERE id_game=? AND day=? 
                  ORDER BY id_vote DESC";

        $res = Database::query($query, [$id_game, $day]);
        if (!$res)
            return false;
        return $res;
    }

    /**
     * Verifica se l'utente votato è valido per il personaggio durante il giorno
     * @param string $username Username o 'flag' dell'utente votato
     * @return boolean Ritorna true se il voto è valido. False altrimenti
     */
    public function checkVoteDay($username) {
        $user = User::fromUsername($username);
        if (!$user)
            return false;
        $status = Role::getRoleStatus($this->engine->game, $user->id_user);
        if ($status == RoleStatus::Dead)
            return false;
        return $username != $this->user->username;
    }
    
    // ------------- SEZIONE INTERNA ---------------

    /**
     * Ottiene le informazioni associate al ruolo
     * @return array|boolean Ritorna un vettore con le informazioni del ruolo 
     * salvate. False se si verifica un errore.
     */
    protected function getData() {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;

        $data = false;

        if (Database::$mongo) {
            $d = Database::$mongo->roles->findOne(["id_game" => $id_game, "id_user" => $id_user]);
            if ($d)
                $data = $d["data"];
        } else {
            $query = "SELECT data FROM player WHERE id_game=? AND id_user=?";
            $res = Database::query($query, [$id_game, $id_user]);

            if (!$res || count($res) != 1)
                return false;

            $data = json_decode($res[0]["data"], true);
            if (!$data) {
                logEvent("I dati dell'utente {$this->user->username} sono danneggiati", LogLevel::Notice);
                return false;
            }
        }

        return $data;
    }

    /**
     * Salva le informazioni del ruolo nel database. Non si deve eccedere con la
     * quantità di informazioni da salvare, lo spazio è limitato...
     * @param array $data Vettore da salvare nel database. Verrà convertito in
     * JSON e la lunghezza non deve superare la dimensione massima consentita
     * @return boolean True se l'operazione ha esito positivo, false altrimenti
     */
    protected function setData($data) {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;

        if (Database::$mongo) {
            $res = Database::$mongo->roles->updateOne(
                ["id_game" => $id_game, "id_user" => $id_user],
                [ '$set' => ["data" => $data] ],
                ["upsert" => true]);
            return $res->getUpsertedCount() + $res->getModifiedCount() == 1;
        } else {
            $json = json_encode($data);

            $query = "UPDATE player SET data=? WHERE id_game=? AND id_user=?";
            $res = Database::query($query, [$json, $id_game, $id_user]);
            return !!$res;
        }
    }

    /**
     * Ottiene il voto del giocatore durante l'istante attuale
     * @return boolean|int Ritorna false se l'utente non ha votato, un intero 
     * contenente il voto altrimenti
     */
    protected function getVote() {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;
        $day = $this->engine->game->day;

        $query = "SELECT vote FROM vote WHERE id_game=? AND id_user=? AND day=?";
        $res = Database::query($query, [$id_game, $id_user, $day]);

        if (!$res || count($res) != 1)
            return false;
        return $res[0]["vote"];
    }

    /**
     * Uccide un personaggio se non è già morto o se non è protetto
     * @param \User $user Utente da uccidere
     * @return boolean True se l'uccisione è avvenuta, false altrimenti
     */
    protected function kill($user) {
        $status = $this->roleStatus($user->id_user);
        // se si è verificato un errore nel capire lo stato dell'utente, non fa nulla
        if (is_bool($status) && !$status) {
            logEvent("Non è stato determinato lo stato di {$user->username}", LogLevel::Warning);
            return false;
        }
        if ($status == RoleStatus::Dead) {
            logEvent("E' stato ucciso un giocatore morto ({$this->user->username} => {$user->username})", LogLevel::Debug);
            return false;
        }
        if ($this->isProtected($user, $this->user)) {
            logEvent("Si ha cercato di uccidere un giocatore protetto ({$this->user->username} => {$user->username})", LogLevel::Debug);
            return false;
        }

        $id_game = $this->engine->game->id_game;
        $id_user = $user->id_user;
        $status = RoleStatus::Dead;

        $query = "UPDATE player SET status=? WHERE id_game=? AND id_user=?";
        $res = Database::query($query, [$status, $id_game, $id_user]);
        if (!$res)
            return false;
        return true;
    }

    /**
     * Verifica se il personaggio è ancora vivo
     * @param int $id_user Identificativo dell'utente da controllare. Se null, utente
     * corrente
     * @return \RoleStatus Ritorna lo stato del personaggio
     */
    protected function roleStatus($id_user = null) {
        if (!$id_user)
            $id_user = $this->user->id_user;
        return Role::getRoleStatus($this->engine->game, $id_user);
    }

    /**
     * Visita un personaggio. Alcuni ruoli necessitano di questa informazione
     * @param \User $visited Utente visitato
     */
    protected function visit($visited) {
        if (isset($this->engine->visited[$visited->id_user]))
            $this->engine->visited[$visited->id_user][] = $this->user->id_user;
        else
            $this->engine->visited[$visited->id_user] = array($this->user->id_user);
    }
    
    /**
     * Rimuove la visita di un personaggio
     * @param \User $visited Utente già visitato
     */
    protected function unvisit($visited) {
        if (!isset($this->engine->visited[$visited->id_user]))
            return;
        $this->engine->visited[$visited->id_user] = 
                array_diff($this->engine->visited[$visited->id_user], array($this->user->id_user));
    }


    /**
     * Ottiene la lista degli utenti che hanno visitato un utente
     * @param \User $user Utente che ha subito le visite
     */
    protected function getVisited($user) {
        if (isset($this->engine->visited[$user->id_user]))
            return $this->engine->visited[$user->id_user];
        return array();
    }
    
    /**
     * Ottiene la lista degli utenti visitati da un utente
     * @param \User $user Utente che ha effettuato le visite
     */
    protected function getVisitedBy($user) {
        $visited = array();
        foreach ($this->engine->visited as $visit => $visitors) 
            if (in_array($user->id_user, $visitors))
                $visited[] = $visit;
        return $visited;
    }


    /**
     * Effettua la votazione di un personaggio
     * @param int $username Utente votato
     * @return boolean True se la votazione ha avuto successo, false altrimenti
     */
    public function vote($username) {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;
        $day = $this->engine->game->day;
        
        $user_voted = User::fromUsername($username);
        if (!$user_voted) {
            logEvent("L'utente $id_user ha votato l'utente $username che non esiste. id_game=$id_game", LogLevel::Warning);
            return false;
        }
        $vote = $user_voted->id_user;
        
        $query = "INSERT INTO vote (id_game,id_user,vote,day) VALUE (?, ?, ?, ?)";
        $res = Database::query($query, [$id_game, $id_user, $vote, $day]);
        if (!$res) {
            logEvent("Impossibile compiere la votazione di $id_user => $username. id_game=$id_game", LogLevel::Warning);
            return false;
        }
        return true;
    }
        
    // ---------------- TOOL DI PROTEZIONE -----------------

    /**
     * Protegge un utente dall'uccisione da un altro utente
     * @param int $id_user Identificativo dell'utente protetto
     * @param int $id_killer Identitificativo dell'utente che potrebbe uccidere
     */
    protected function protectUserFromUser($id_user, $id_killer) {
        $this->protect("@" . $id_user, "@" . $id_killer);
    }

    /**
     * Protegge un utente dall'uccisione da un ruolo
     * @param int $id_user Identificativo dell'utente protetto
     * @param string $role_name Nome del ruolo che potrebbe uccidere
     */
    protected function protectUserFromRole($id_user, $role_name) {
        $this->protect("@" . $id_user, "#" . $role_name);
    }

    /**
     * Protegge ruolo dall'uccisione da un utente     
     * @param string $role_name Nome del ruolo da proteggere
     * @param int $id_user Identificativo dell'utente che potrebbe uccidere
     */
    protected function protectRoleFromUser($role_name, $id_user) {
        $this->protect("#" . $role_name, "@" . $id_user);
    }

    /**
     * Protegge ruolo dall'uccisione da un ruolo     
     * @param string $role_name Nome del ruolo da proteggere
     * @param string $role_name_killer Nome del ruolo che potrebbe uccidere
     */
    protected function protectRoleFromRole($role_name, $role_name_killer) {
        $this->protect("#" . $role_name, "#" . $role_name_killer);
    }

    /**
     * Protegge tutti da un utente
     * @param int $id_user Identificativo dell'utente che potrebbe uccidere
     */
    protected function protectAllFromUser($id_user) {
        $this->protect("*", "@" . $id_user);
    }

    /**
     * Protegge un utente da tutti
     * @param int $id_user Identificativo dell'utente protetto
     */
    protected function protectUserFromAll($id_user) {
        $this->protect("@" . $id_user, "*");
    }

    /**
     * Protegge tutti da un ruolo
     * @param string $role_name Nome del ruolo che potrebbe uccidere
     */
    protected function protectAllFromRole($role_name) {
        $this->protect("*", "#" . $role_name);
    }

    /**
     * Protegge ruolo dall'uccisione da tutti
     * @param string $role_name Nome del ruolo da proteggere
     */
    protected function protectRoleFromAll($role_name) {
        $this->protect("#" . $role_name, "*");
    }

    /**
     * Protegge tutti dall'uccisione da tutti
     */
    protected function protectAllFromAll() {
        $this->protect("*", "*");
    }

    /**
     * Protegge un ruolo/utente da un ruolo/utente
     * @param string $protected Il ruolo/personaggio protetto
     * @param string $killer Il ruolo/personaggio killer
     */
    private function protect($protected, $killer) {
        if (!isset($this->engine->protected[$protected]))
            $this->engine->protected[$protected] = array();
        $this->engine->protected[$protected][] = $killer;
    }

    /**
     * Verifica se $killer può uccidere $user
     * @param \User $user Utente da uccidere
     * @param \User $killer Utente che può uccidere
     * @return bool True se l'utente è protetto, false altrimenti
     */
    protected function isProtected($user, $killer) {
        $id_user = "@" . $user->id_user;
        $role_user = "#" . Role::getRole($user, $this->engine->game);
        $id_killer = "@" . $killer->id_user;
        $role_killer = "#" . Role::getRole($killer, $this->engine->game);

        if (isset($this->engine->protected[$id_user]))
            if (in_array($id_killer, $this->engine->protected[$id_user]) ||
                    in_array($role_killer, $this->engine->protected[$id_user]) ||
                    in_array("*", $this->engine->protected[$id_user]))
                return true;
        if (isset($this->engine->protected[$role_user]))
            if (in_array($id_killer, $this->engine->protected[$role_user]) ||
                    in_array($role_killer, $this->engine->protected[$role_user]) ||
                    in_array("*", $this->engine->protected[$role_user]))
                return true;
        if (isset($this->engine->protected["*"]))
            if (in_array($id_killer, $this->engine->protected["*"]) ||
                    in_array($role_killer, $this->engine->protected["*"]) ||
                    in_array("*", $this->engine->protected["*"]))
                return true;
        return false;
    }

    /**
     * Confronta due ruoli per ordinarli in base alla loro priorità
     * @param \Role $roleA 
     * @param \Role $roleB
     * @return int Ritorna il valore del contronto delle priorità dei ruoli
     */
    static function cmpRole($roleA, $roleB) {
        $priA = $roleA->getPriority();
        $priB = $roleB->getPriority();
        if ($priA == $priB)
            return 0;
        return ($priA < $priB) ? -1 : 1;
    }

    /**
     * Ottiene la priorità del ruolo
     * @return int La priorità del ruolo
     */
    function getPriority() {
        $class_name = get_class($this);
        return $class_name::$priority;
    }

    /**
     * Ottiene il nome del ruolo
     * @return string Il nome breve del ruolo
     */
    function getRoleName() {
        $class_name = get_class($this);
        return $class_name::$role_name;
    }

    /**
     * Ottiene una stringa con il nome del ruolo associato ad un giocatore nella 
     * partita
     * @param \User $user L'utente da controllare
     * @param \Game $game La partita in cui controllare
     * @return string|boolean Ritorna il ruolo del giocatore, false in caso di errore
     */
    public static function getRole($user, $game) {
        $id_user = $user->id_user;
        $id_game = $game->id_game;

        $query = "SELECT role FROM player WHERE id_user=? AND id_game=?";
        $res = Database::query($query, [$id_user, $id_game]);

        if (!$res || count($res) != 1) {
            logEvent("L'utente $id_user non è nella partita $id_game", LogLevel::Warning);
            return false;
        }
        return $res[0]["role"];
    }
    
    /**
     * Crea una nuova istanza di \Role dall'utente e dal motore della partita
     * @param \User $user Utente a cui appartiene il ruolo
     * @param \Engine $engine Motore della partita
     * @return \Role|boolean Ritorna il ruolo dell'utente. False se si verifica un errore
     */
    public static function fromUser($user, $engine) {
        $role_name = Role::getRole($user, $engine->game);
        if ($role_name == "unknown")
            return false;
        // deve esistere una classe con quel nome e deve derivare da "Role"
        if (!class_exists($role_name) || !in_array("Role", class_parents($role_name))) {
            logEvent("Il ruolo '$role_name' di '{$user->username}' nella partita {$engine->game->id_game} non è valido", LogLevel::Error);
            return false;
        }
        // usa il contenuto di ($role_name) come nome della classe da dichiarare
        $role = new $role_name($user, $engine);
        // il ruolo deve essere abilitato
        if (!$role_name::$enabled) {
            logEvent("Il ruolo '$role_name' di '{$user->username}' nella partita {$engine->game->id_game} non è abilitato", LogLevel::Error);
            return false;
        }
        return $role;
    }

    /**
     * Verifica se un personaggio è ancora vivo
     * @param \Game $game Gioco in cui il personaggio si trova
     * @param int $id_user Identificativo dell'utente a cui corrisponde il personaggio
     * @return \RoleStatus Ritorna lo stato del personaggio
     */
    public static function getRoleStatus($game, $id_user) {
        $id_game = $game->id_game;

        $query = "SELECT status FROM player WHERE id_game=? AND id_user=?";
        $res = Database::query($query, [$id_game, $id_user]);
        if (!$res || count($res) != 1) {
            logEvent("L'utente $id_user non è nella partita $id_game", LogLevel::Warning);
            return false;
        }
        return $res[0]["status"];
    }

    /**
     * Verifica se un ruolo esiste ed è utilizzabile
     * @param string $roleName Nome della classe del ruolo
     * @param \User $user [Optional]<br> Utente che deve usare il ruolo. Per il
     * controllo delle funzioni di debug
     * @return boolean True se il ruolo è disponibile, false se non esiste o 
     * non è utilizzabile
     */
    public static function roleExists($roleName, $user = null) {
        // verifica se esiste la classe e se deriva da Role
        if (!class_exists($roleName) || !in_array("Role", class_parents($roleName)))
            return false;
        // verifica che il ruolo sia abilitato
        if (!$roleName::$enabled)
            return false;
        // se il ruolo è in debug allora controllo se l'utente ha i permessi per usarlo
        if ($roleName::$debug && $user) {
            $level = Level::getLevel($user->level);
            if ($level->betaFeature)
                return true;
            return false;
        }
        return true;
    }
}
