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

    /**
     * Nome breve del ruolo. Identifica il ruolo. La definizione del ruolo
     * è nel percorso: roles/role.Xxxx.php e il ruolo è una classe di nome Xxxx
     * con Xxxx il role_name con l'iniziale maiuscola
     * @var string
     */
    public $role_name = "";

    /**
     * Nome completo del ruolo
     * @var string
     */
    public $name = "";

    /**
     * Indica se questo ruolo è accessibile solo agli utenti con le funzioni di
     * debug sbloccate
     * @var boolean
     */
    public $debug = false;

    /**
     * Indica se questo ruolo è abilitato
     * @var boolean
     */
    public $enabled = true;

    /**
     * Priorità di esecuzione delle operazioni dei vari ruoli. Valori inferiori
     * hanno priorità maggiori
     * @var int
     */
    public $priority = 1000;

    /**
     * Squadra di appartenenza del ruolo
     * @var \RoleTeam
     */
    public $team = RoleTeam::Villages;

    /**
     * Tipo di mana del ruolo
     * @var \Mana
     */
    public $mana = Mana::Good;

    /**
     * Utente a cui appartiene il ruolo
     * @var \User
     */
    protected $user;

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
     * Questa funzione viene chiamata solo durante l'arrivo al villaggio. Mostra
     * all'utente delle informazioni utili riguardo il ruolo.
     * @return boolean|string False se non è necessario mostrare alcun messaggio.
     * Altrimenti contiene una stringa HTML da includere nella pagina
     */
    public abstract function splash();

    /**
     * Questa funzione deve ritornare un valore booleano che indica se la partita
     * è in attesa del voto di questo personaggio in questa notte
     * @return boolean|string False se la partita può continuare, altrimenti
     * contiene una stringa HTML da aggiungere alla pagina contenente il form da
     * completare
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
     * Questa funzione deve ritornare un valore booleano che indica se la partita
     * è in attesa del voto di questo personaggio in questa giorno. Può essere 
     * modificata per adattarsi alle specifiche di personaggi che aviscono di 
     * giorno. E' implementata la funzione di un normale personaggio che vota 
     * chi mettere al rogo
     * @return boolean|string False se la partita può continuare, altrimenti
     * contiene una stringa HTML da aggiungere alla pagina contenente il form da
     * completare
     */
    public function needVoteDay() {
        // un utente morto non vota
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote)
            return "vote";
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
        if (!$dead)
            return false;

        // quorum
        if ($num_votes >= (int) (count($votes) * 0.5) + 1)
            $this->kill($dead);

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

        $query = "SELECT id_user,vote FROM vote WHERE "
                . "id_game=$id_game AND day=$day "
                . "ORDER BY id_vote DESC";

        $res = Database::query($query);
        if (!$res)
            return false;
        return $res;
    }

    /**
     * Ottiene le informazioni associate al ruolo
     * @return array|boolean Ritorna un vettore con le informazioni del ruolo 
     * salvate. False se si verifica un errore.
     */
    protected function getData() {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;

        $query = "SELECT data FROM role WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res || count($res) != 1)
            return false;
        $data = json_decode($res[0], true);
        if (!$data)
            return false;
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
        $json = Database::escape(json_encode($data));

        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;

        $query = "UPDATE role SET data='$json' WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
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

        $query = "SELECT vote FROM vote WHERE id_game=$id_game AND id_user=$id_user AND day=$day";
        $res = Database::query($query);

        if (!$res || count($res) != 1)
            return false;
        return $res[0]["vote"];
    }

    /**
     * Uccide un personaggio se non è già morto o se non è protetto
     * @param \User $user Identificativo dell'utente da uccidere
     * @return boolean True se l'uccisione è avvenuta, false altrimenti
     */
    protected function kill($user) {
        $status = $this->roleStatus($user->id_user);
        // se si è verificato un errore nel capire lo stato dell'utente, non fa nulla
        if (is_bool($status) && !$status)
            return false;
        if ($status == RoleStatus::Dead)
            return false;

        if ($this->isProtected($user, $this->user))
            return false;

        $id_game = $this->engine->game->id_game;
        $id_user = $user->id_user;
        $status = RoleStatus::Dead;

        $query = "UPDATE role SET status=$status WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }

    /**
     * Verifica se il personaggio è ancora vivo
     * @param int $name Identificativo dell'utente da controllare. Se null, utente
     * corrente
     * @return \RoleStatus Ritorna lo stato del personaggio
     */
    protected function roleStatus($id_user = null) {
        $id_game = $this->engine->game->id_game;
        if (!$id_user)
            $id_user = $this->user->id_user;

        $query = "SELECT status FROM role WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res || count($res) != 1)
            return false;

        return $res[0]["status"];
    }

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
        if ($roleA->priority == $roleB->priority)
            return 0;
        return ($roleA->priority < $roleB->priority) ? -1 : 1;
    }

    /**
     * Ottiene una stringa con il nome del ruolo associato ad un giocatore nella 
     * partita
     * @param \User $user L'utente da controllare
     * @param \Game $game La partita in cui controllare
     */
    public static function getRole($user, $game) {
        $id_user = $user->id_user;
        $id_game = $game->id_game;

        $query = "SELECT role FORM role WHERE id_user=$id_user AND id_game=$id_game";
        $res = Database::query($query);

        if (!$res || count($res) != 1)
            return false;
        return $res[0]["role"];
    }

}
