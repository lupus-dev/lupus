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
    const NextDay = 1;

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
     * La partita non è in corso. Nessuna azione è stata compiuta
     */
    const BadGameStatus = 503;

    /**
     * La partita è terminata perchè i ruoli non sono stati generati correttamente
     */
    const BadRoleAssign = 504;

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
     * Vettore che contiene l'elenco delle visite dei giocatori
     * @var array La chiave del vettore è l'utente visitato, il valore è un 
     * vettore degli utenti che hanno visitato l'utente. Gli utenti sono riferiti
     * con il loro identificativo
     */
    public $visited;

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

        $gameStatus = $this->game->status;
        if ($gameStatus < 100 || $gameStatus >= 200) {
            logEvent("La partita non è in corso", LogLevel::Notice);
            return Engine::BadGameStatus;
        }
        if ($gameStatus == GameStatus::NotStarted && $this->game->day != 0) {
            logEvent("La partita non è ancora iniziata ma il giorno non è zero. Day={$this->game->day}", LogLevel::Error);
            $this->game->status(GameStatus::TermByBug);
            return Engine::BadGameStatus;
        }

        if ($gameStatus == GameStatus::Running) {
            $roles = $this->getAllRoles();
            if (!$roles) {
                logEvent("Impossibile recuperare i ruoli. Partita terminata: codice " . Engine::BadRole, LogLevel::Error);
                $this->game->status(GameStatus::TermByBug);
                return Engine::BadRole;
            }
        } else if ($gameStatus == GameStatus::NotStarted)
            $roles = array();

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
            logEvent("Un'azione non è terminata correttamente. Partita terminata: codice $performStatus", LogLevel::Error);
            $this->game->status(GameStatus::TermByBug);
            return $performStatus;
        }

        // verifica se la partita termina
        $endStatus = $this->checkEnd();
        // se il giorno/notte è finito correttamente
        if ($endStatus == Engine::NextDay) {
            logEvent("Il giorno/notte è finito correttamente", LogLevel::Debug);
            $this->game->nextDay();
            return Engine::NextDay;
        }
        // se la partita termina
        if ($endStatus <= GameStatus::DeadWin) {
            logEvent("La partita è terminata correttamente: codice $endStatus", LogLevel::Debug);
            $this->game->status($endStatus);
            return Engine::EndGame;
        }
        // se se verifica un errore
        $this->game->status(GameStatus::TermByBug);
        logEvent("Un errore strano è accaduto, la partita è stata terminata", LogLevel::Error);
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
        $query = "SELECT id_user,role FROM player WHERE id_game=?";
        $res = Database::query($query, [$id_game]);
        if (!$res)
            return false;

        $roles = array();
        $this->roles = array();

        foreach ($res as $role) {
            $id_user = $role["id_user"];
            $user_role = $role["role"];

            $user = User::fromIdUser($id_user);

            $role = Role::fromUser($user, $this);

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
            case GameTime::Start:
                // se la partita non è al completo allora aspetta altri giocatori
                if ($this->game->getNumPlayers() < $this->game->num_players)
                    return Engine::NeedVote;
                // altrimenti può continuare
                return false;
            default:
                logEvent("Tempo non riconosciuto ({$this->game->day} => $time)", LogLevel::Warning);
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
        $time = GameTime::fromDay($this->game->day);
        switch ($time) {
            case GameTime::Night:
                foreach ($roles as $role)
                    if (!$role->performActionNight()) {
                        logEvent("L'azione notturna di '{$role->user->username}' ({$role->getRoleName()}) è fallita", LogLevel::Error);
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            case GameTime::Day:
                foreach ($roles as $role)
                    if (!$role->performActionDay()) {
                        logEvent("L'azione diurna di '{$role->user->username}' ({$role->getRoleName()}) è fallita", LogLevel::Error);
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            case GameTime::Start:
                // distribuisce i ruoli
                if (!RoleDispenser::Compute($this->game))
                    return Engine::BadRoleAssign;
                // ottiene i ruoli renerati e avvia la partita
                $this->getAllRoles();
                $this->game->status(GameStatus::Running);
                break;
            default:
                logEvent("Tempo non riconosciuto ({$this->game->day} => $time)", LogLevel::Warning);
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
            // se c'è un ruolo non riconosciuto
            if (!class_exists($role_name) || !in_array("Role", class_parents($role_name))) {
                logEvent("Ruolo $role_name non riconosciuto, skipped", LogLevel::Warning);
                continue;
            }
            $team_name = $role_name::$team_name;
            $teams[$team_name] = $team_name;
        }
        usort($teams, array("Team", "cmpTeam"));
        foreach ($teams as $team_name) {
            $team_name = firstUpper($team_name);
            // se la squadra non è riconosciuta
            if (!class_exists($team_name) || !in_array("Team", class_parents($team_name))) {
                logEvent("La squadra '$team_name' non è valida", LogLevel::Error);
                return Engine::BadTeam;
            }
            $team_obj = new $team_name($this);
            // se la squadra ha vinto
            if ($team_obj->checkWin()) {
                logEvent("La squadra {$team_name::$name} ha vinto", LogLevel::Debug);
                return GameStatus::Winy + $team_name::$team_code;
            }
        }
        // se tutti i giocatori sono morti, forza la fine della partita
        if ($this->checkDeadEnd()) {
            logEvent("La partita è terminata perchè sono tutti morti", LogLevel::Notice);
            return GameStatus::DeadWin;
        }
        return Engine::NextDay;
    }

    /**
     * Verifica se la partita è terminata perchè tutti i giocatori sono morti
     * @return boolean
     */
    private function checkDeadEnd() {
        $id_game = $this->game->id_game;
        $alive = RoleStatus::Alive;

        $query = "SELECT COUNT(*) AS alive FROM player WHERE id_game=? AND status=?";
        $res = Database::query($query, [$id_game, $alive]);
        if (!$res || count($res) != 1)
            return false;

        // se ci sono zero giocatori vivi, la partita termina
        return $res[0]["alive"] == 0;
    }

}
