<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


/**
 * Classe che rappresenta il ruolo Guardia
 */
class Guardia extends Role {
    
    public static $role_name = "guardia";
    public static $name = "Guardia";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 50;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $gen_probability = 1; 
    public static $gen_number = 1;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    /**
     * Verifica se la guardia deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {        
        // un lupo morto non vota
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $alive = $this->engine->game->getAlive();
            $votable = array();
            foreach ($alive as $user)
                if ($user->id_user != $this->user->id_user) 
                    $votable[] = $user->username;
            return array(
                "votable" => $votable,
                "pre" => "<p>Vota chi proteggere!</p>"
            );
        }
        return false;
    }
    
    /**
     * Esegue l'azione associata alla guardia: protegge l'utente votato dai lupi
     * @return boolean Ritorna true se tutto è andato per il verso giusto. False
     * se il giocatore da proteggere non esiste
     */
    public function performActionNight() {
        $vote = $this->getVote();
        $voted = User::fromIdUser($vote);
        if (!$voted)
            return false;
        $this->protectUserFromRole($voted->id_user, Lupo::$role_name);
        // la guardia visita un giocatore quando lo protegge
        $this->visit($voted);
        return true;
    }

    /**
     * Ritornerà l'HTML dello splash all'arrivo al villaggio
     * @return string Stringa HTML da includere nella pagina
     */
    public function splash() {
        return "Sei una guardia...";
    }

    /**
     * Verifica se l'utente votato esiste, è vivo e non è l'utente
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        $normal = parent::checkVoteNight($username);
        if (!$normal)
            return false;
        return $username != $this->user->username;
    }
}
