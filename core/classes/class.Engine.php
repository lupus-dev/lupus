<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che si occupa di far muovere una partita
 */
class Engine {

    /**
     * La partita è avanzata di un giorno/notte
     */
    const Ok = 1;

    /**
     * La partita non è avanzata a causa di alcuni voti richiesti
     */
    const NeedVote = 2;

    /**
     * La partita è terminata in modo corretto
     */
    const EndGame = 3;

    /**
     * La partita è stata terminata a causa di un ruolo non riconosciuto
     */
    const BadRole = 500;

    /**
     * La partita è stata terminata a causa di un'azione fallita
     */
    const BadAction = 501;

    /**
     * La partita è stata terminata a causa di una squadra non riconosciuta
     */
    const BadTeam = 502;

    /**
     * La partita da far muovere
     * @var \Game
     */
    public $game;

    /**
     * Vettore che contiene un elenco di giocatori/ruoli protetti
     * @var array
     */
    public $protected;

    /**
     * Vettore che contiene gli identificativi dei giocatori indicizzati per 
     * ruolo
     * @var array
     */
    public $roles;

    /**
     * Crea un nuovo motore basandosi sulla partita specificata
     * @param \Game $game Partita in cui il motore girerà
     */
    public function __construct($game) {
        logEvent("-----------------------------------------------------------", LogLevel::Debug);
        logEvent("Creato Engine: room={$game->id_room} game={$game->game_name}({$game->id_game})", LogLevel::Debug);
        $this->game = $game;
    }

    /**
     * Esegue il motore della partita. Fa avazare il gioco se serve
     * @return int Ritorna un valore che indica il codice di uscita del motore
     * (vedi costanti in \Engine)
     */
    public function run() {
        logEvent("Engine avviato", LogLevel::Verbose);
        $roles = $this->getAllRoles();
        if (!$roles) {
            logEvent("Impossibile recuperare i ruoli. Partita terminata: codice " . Engine::BadRole, LogLevel::Warning);
            $this->game->status(GameStatus::TermByBug);
            return Engine::BadRole;
        }
        
        // controlla se qualcuno deve ancora votare
        $voteStatus = $this->checkVotes($roles);
        if ($voteStatus) {
            logEvent("Alcuni giocatori non hanno votato: codice $voteStatus", LogLevel::Debug);
            return $voteStatus;
        }
        // ordina i ruoli per priorità. Quelli con priorità uguale hanno ordine
        // casuale
        shuffle($roles);
        usort($roles, array("Role", "cmpRole"));

        // esegue le azioni associate agli utenti
        $performStatus = $this->performAction($roles);
        if ($performStatus) {
            logEvent("Un'azione non è terminata correttamente. Partita terminata: codice $performStatus", LogLevel::Warning);
            return $performStatus;
        }
        // verifica se la partita termina
        $endStatus = $this->checkEnd();
        // se il giorno/notte è finito correttamente
        if ($endStatus == Engine::Ok) {
            logEvent("Il giorno/notte è finito correttamente", LogLevel::Debug);
            $this->game->nextDay();
            return Engine::Ok;
        }
        // se la partita termina
        if ($endStatus <= GameStatus::DeadWin) {
            logEvent("La partita è terminata correttamente: codice $endStatus", LogLevel::Debug);
            $this->game->status($endStatus);
            return Engine::EndGame;
        }
        // se se verifica un errore
        $this->game->status(GameStatus::TermByBug);
        return $endStatus;
    }

    /**
     * Ottiene un vettore di Role contentente tutti i giocatori della partita
     * @return array|boolean False se si verifica un errore. Un vettore con tutti
     * i giocatori altrimenti
     */
    private function getAllRoles() {
        $id_game = $this->game->id_game;
        // query unica per estrarre tutti i ruoli
        $query = "SELECT id_user,role FROM role WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        
        $roles = array();
        $this->roles = array();

        foreach ($res as $role) {
            $id_user = $role["id_user"];
            $user_role = $role["role"];

            $user = User::fromIdUser($id_user);
            // il nome del ruolo è il codice del ruolo con l'iniziale maiuscola
            $role_name = firstUpper($user_role);
            // deve esistere una classe con quel nome e deve derivare da "Role"
            if (!class_exists($role_name) || !in_array("Role", class_parents($role_name))) {
                logEvent("Il ruolo '$role_name' di '{$user->username}' nella partita {$this->game->id_game} non è valido", LogLevel::Error);
                return false;
            }
            // usa il contenuto di ($role_name) come nome della classe da dichiarare
            $role = new $role_name($user, $this);
            // il ruolo deve essere abilitato
            if (!$role_name::$enabled) {
                logEvent("Il ruolo '$role_name' di '{$user->username}' nella partita {$this->game->id_game} non è abilitato", LogLevel::Error);
                return false;
            }
            // memorizzo l'utente nell'elenco di utenti per ruolo
            if (!isset($this->roles[$user_role]))
                $this->roles[$user_role] = array($id_user);
            else
                $this->roles[$user_role][] = $id_user;

            $roles[] = $role;
        }

        return $roles;
    }

    /**
     * Verifica che tutti i giocatori abbiano votato
     * @param array $roles Vettore con i ruoli dei giocatori
     * @return boolean|int False se tutti i giocatori hanno votato, un codice di 
     * errore altrimenti
     */
    private function checkVotes($roles) {
        // suddivide i controlli in base al tempo della partita
        $time = GameTime::fromDay($this->game->day);
        switch ($time) {
            case GameTime::Night:
                foreach ($roles as $role)
                    if ($role->needVoteNight())
                        return Engine::NeedVote;
                break;
            case GameTime::Day:
                foreach ($roles as $role)
                    if ($role->needVoteDay())
                        return Engine::NeedVote;
                break;
            default:
                logEvent("Tempo non riconosciuto ({$this->game->day} => $time)", LogLevel::Notice);
                break;
        }
        logEvent("Tutti i giocatori hanno votato", LogLevel::Debug);
        return false;
    }

    /**
     * Esegue le azioni associate ad ogni ruolo in base all'ordine con cui sono 
     * in $roles
     * @param array $roles Vettore con i ruoli dei giocatori
     * @return boolean|int False se non ci sono stati errori, un codice di 
     * errore altrimenti
     */
    private function performAction($roles) {
        // in base alla priorità esegue le azioni associate a ciascun ruolo
        // suddivide le azioni in base al tempo della partita
        switch (GameTime::fromDay($this->game->day)) {
            case GameTime::Night:
                foreach ($roles as $role)
                    if (!$role->performActionNight()) {
                        logEvent("L'azione notturna di '{$role->user->username}' ({$role->getRoleName()}) è fallita", LogLevel::Warning);
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            case GameTime::Day:
                foreach ($roles as $role)
                    if (!$role->performActionDay()) {
                        logEvent("L'azione diurna di '{$role->user->username}' ({$role->getRoleName()}) è fallita", LogLevel::Warning);
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            default:
                logEvent("Tempo non riconosciuto ({$this->game->day} => $time)", LogLevel::Notice);
                break;
        }
        logEvent("Tutti i giocatori hanno eseguito", LogLevel::Debug);
        return false;
    }

    /**
     * Verifica se la partita è terminata
     * @return int Ritorna un valore che identifica lo stato del motore
     */
    private function checkEnd() {
        // elenco di tutti ruoli nella partita
        $roles = array_keys($this->roles);
        $teams = array();
        // estrae i nomi delle squadre della partita
        foreach ($roles as $role) {
            $role_name = firstUpper($role);
            $team_name = $role_name::$team_name;
            $teams[$team_name] = $team_name;
        }
        foreach ($teams as $team_name) {
            $team = firstUpper($team_name);
            // se la squadra non è riconosciuta
            if (!class_exists($team) || !in_array("Team", class_parents($team))) {
                logEvent("La squadra '$team' non è valida", LogLevel::Error);
                return Engine::BadTeam;
            }
            $team_obj = new $team($this);
            // se la squadra ha vinto
            if ($team_obj->checkWin()) {
                logEvent("La squadra {$team::$name} ha vinto", LogLevel::Debug);
                return GameStatus::Winy + $team::$team_code;
            }
        }
        // se tutti i giocatori sono morti, forza la fine della partita
        if ($this->checkDeadEnd()) {
            logEvent("La partita è terminata perchè sono tutti morti", LogLevel::Debug);
            return GameStatus::DeadWin;
        }
        return Engine::Ok;
    }

    /**
     * Verifica se la partita è terminata perchè tutti i giocatori sono morti
     * @return boolean
     */
    private function checkDeadEnd() {
        $id_game = $this->game->id_game;
        $alive = RoleStatus::Alive;
        
        $query = "SELECT COUNT(*) AS alive FROM role WHERE id_game=$id_game AND status=$alive";
        $res = Database::query($query);
        if (!$res || count($res) != 1)
            return false;
        
        // se ci sono zero giocatori vivi, la partita termina
        return $res[0]["alive"] == 0;
    }

}
