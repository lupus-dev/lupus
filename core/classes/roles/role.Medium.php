<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Medium
 */
class Medium extends Role {

    public static $role_name = "medium";
    public static $name = "Medium";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 150;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $gen_probability = 1;
    public static $gen_number = 1;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    /**
     * Verifica se il medium deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $dead = $this->engine->game->getDead();
            // se non c'è nessun morto, non vota
            if (!$dead)
                return false;
            $votable = array();
            foreach ($dead as $user)
                $votable[] = $user->username;            
            $votable = array_merge(array("(nessuno)"), $votable);
            return array(
                "votable" => $votable,
                "pre" => "<p>Vota chi guardare!</p>"
            );
        }
        return false;
    }

    /**
     * Esegue l'azione associata al medium, inserisce negli eventi della partita
     * la visione del medium
     * @return boolean Ritorna true se tutto è andato per il verso giusto. False
     * se il giocatore da vedere non esiste
     */
    public function performActionNight() {
        // se l'utente è morto non agisce
        if ($this->getRoleStatus($this->engine->game, $this->user->id_user) == RoleStatus::Dead)
            return true;
        $vote = $this->getVote();
        if ($vote == 0)
            return true;
        $voted = User::fromIdUser($vote);
        if (!$voted)
            return false;
        Event::insertMediumAction($this->engine->game, $this->user, $voted);
        // il medium non visita l'utente...
        return true;
    }

    public function splash() {
        return "Sei un medium";
    }

    /**
     * Verifica se il voto dell'utente è valido
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        if ($username == "(nessuno)")
            return true;
        $user = User::fromUsername($username);
        if (!$user) return false;
        $status = Role::getRoleStatus($this->engine->game, $user->id_user);
        if ($status == RoleStatus::Alive)
            return false;
        return $username != $this->user->username;
    }

    /**
     * Effettua la votazione di un personaggio. Il medium può votare '(nessuno)',
     * in questo caso l'utente votato ha identificativo 'zero'
     * @param int $username Utente votato
     * @return boolean True se la votazione ha avuto successo, false altrimenti
     */
    public function vote($username) {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;
        $day = $this->engine->game->day;

        if ($username != "(nessuno)") {
            $user_voted = User::fromUsername($username);
            if (!$user_voted) {
                logEvent("L'utente $id_user ha votato l'utente $username che non esiste. id_game=$id_game", LogLevel::Warning);
                return false;
            }
            $vote = $user_voted->id_user;
        } else 
            $vote = 0;            
                
        $query = "INSERT INTO vote (id_game,id_user,vote,day) VALUE "
                . "($id_game,$id_user,$vote,$day)";
        $res = Database::query($query);
        if (!$res) {
            logEvent("Impossibile compiere la votazione di $id_user => $username. id_game=$id_game", LogLevel::Warning);
            return false;
        }
        return true;
    }

}
