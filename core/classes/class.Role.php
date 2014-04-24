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
     * @var \Team
     */
    public $team = Team::Villages;
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
     * Partita a cui appartiene il ruolo
     * @var \Game
     */
    protected $game;

    /**
     * Costruttore di \Role
     * @param \User $user Utente a cui appartiene il ruolo
     * @param \Game $game Partita a cui appartiene il ruolo
     */
    public function __construct($user, $game) {
        $this->user = $user;
        $this->game = $game;
    }
    
    /**
     * Questa funzione deve ritornare un valore booleano che indica se la partita
     * è in attesa del voto di questo personaggio
     * @return boolean|string False se la partita può continuare, altrimenti
     * contiene una stringa HTML da aggiungere alla pagina contenente il form da
     * completare
     */
    public abstract function needVote();
    /**
     * Effettua l'operazione associata al ruolo durante il cambio giorno/notte
     * @return boolean Ritorna true se l'operazione è stata eseguita con successo.
     * False se si è verificato un errore. Un errore potrebbe interrompere l'intera
     * partita (codice 303).
     */
    public abstract function performAction();
    /**
     * Questa funzione viene chiamata solo durante l'arrivo al villaggio. Mostra
     * all'utente delle informazioni utili riguardo il ruolo.
     * @return boolean|string False se non è necessario mostrare alcun messaggio.
     * Altrimenti contiene una stringa HTML da includere nella pagina
     */
    public abstract function splash();
    /**
     * Ottiene le informazioni associate al ruolo
     * @return array|boolean Ritorna un vettore con le informazioni del ruolo 
     * salvate. False se si verifica un errore.
     */
    protected function getData() {
        $id_game = $this->game->id_game;
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
    protected function setData($data){
        $json = Database::escape(json_encode($data));
        
        $id_game = $this->game->id_game;
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
        $id_game = $this->game->id_game;
        $id_user = $this->user->id_user;
        $day = $this->game->day;
        
        $query = "SELECT vote FROM vote WHERE id_game=$id_game AND id_user=$id_user AND day=$day";
        $res = Database::query($query);
        
        if (!$res || count($res) != 1)
            return false;
        return $res[0]["vote"];
    }
    /**
     * Uccide un personaggio
     * @param int $user Identificativo dell'utente da uccidere
     * @param string $descr Eventuale messaggio di motivazione
     * @return boolean True se l'uccisione è avvenuta, false altrimenti
     */
    protected function kill($user, $descr = "") {
        $status = $this->roleStatus($user);
        if ($status == RoleStatus::Dead)
            return false;
        
        $id_game = $this->game->id_game;
        $id_user = $this->user->id_user;
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
        $id_game = $this->game->id_game;
        if (!$id_user)
            $id_user = $this->user->id_user;
        
        $query = "SELECT status FROM role WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res || count($res) != 1)
            return false;
        
        return $res[0]["status"];
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
}