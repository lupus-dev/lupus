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
     * La partita è stata terminata a causa di un ruolo non riconosciuto
     */
    const BadRole = 500;

    /**
     * La partita è stata terminata a causa di un'azione fallita
     */
    const BadAction = 501;

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
     * Crea un nuovo motore basandosi sulla partita specificata
     * @param \Game $game Partita in cui il motore girerà
     */
    public function __construct($game) {
        $this->game = $game;
    }

    /**
     * Esegue il motore della partita. Fa avazare il gioco se serve
     * @return int Ritorna un valore che indica il codice di uscita del motore
     * (vedi costanti in \Engine)
     */
    public function run() {
        $roles = $this->getAllRoles();
        if (!$roles) {
            $this->game->status(GameStatus::TermByBug);
            return Engine::BadRole;
        }

        $voteStatus = $this->checkVotes($roles);
        if ($voteStatus)
            return $voteStatus;

        // ordina i ruoli per priorità. Quelli con priorità uguale hanno ordine
        // casuale
        shuffle($roles);
        usort($roles, array("Role", "cmpRole"));

        $performStatus = $this->performAction($roles);
        if ($performStatus)
            return $performStatus;

        // @todo Per ogni squadra verificare se la partita termina
        //       Se si, falla terminare con codice 200+y
        //       Se no, fa avanzare il giorno/notte
        return Engine::Ok;
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

        foreach ($res as $role) {
            $id_user = $role["id_user"];
            $role = $role["role"];

            $user = User::fromIdUser($id_user);
            // il nome del ruolo è il codice del ruolo con l'iniziale maiuscola
            $role_name = firstUpper($role);
            // deve esistere una classe con quel nome e deve derivare da "Role"
            if (!class_exists($role_name) || !in_array("Role", class_parents($role_name)))
                return false;
            // usa il contenuto di ($role_name) come nome della classe da dichiarare
            $role = new $role_name($user, $this);
            // il ruolo deve essere abilitato
            if (!$role->enabled)
                return false;
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
        switch (GameTime::fromDay($this->game->day)) {
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
                break;
        }
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
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            case GameTime::Day:
                foreach ($roles as $role)
                    if (!$role->performActionDay()) {
                        $this->game->status(GameStatus::TermByBug);
                        return Engine::BadAction;
                    }
                break;
            default:
                break;
        }
        return false;
    }

}
