<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Lupo
 */
class Lupo extends Role {
    
    public static $role_name = "lupo";
    public static $name = "Lupo";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 100;
    public static $team_name = RoleTeam::Antagonists;
    public static $mana = Mana::Bad;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    /**
     * Verifica se il lupo deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {        
        // un lupo morto non vota
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote)
            return "vote";
        return false;
    }

    /**
     * Cerca i voti di tutti i lupi che hanno votato
     * @return boolean|array Ritorna un vettore con i voti e i votanti lupi che 
     * durante questa notte hanno votato. False se si verifica un errore
     */
    private function getVoteLupus() {
        $id_game = $this->engine->game->id_game;
        $day = $this->engine->game->day;
        $role_name = Lupo::$role_name;
        
        $query = "SELECT id_user,vote FROM vote WHERE "
                . "id_game=$id_game AND day=$day AND "
                . "(SELECT role FROM role WHERE vote.id_user=role.id_user AND role.id_game=$id_game)='$role_name' "
                . "ORDER BY id_vote DESC";
        $res = Database::query($query);
        if (!$res)
            return false;
        return $res;
    }
    
    /**
     * Esegue l'azione associata ai lupi:
     * <ol>
     * <li>Cerca i voti degli altri lupi
     * <li>Se non è stato l'ultimo a votare si ferma
     * <li>Altrimenti ordina i bersagli per numero di voti
     * <li>Se il più votato ha raggiunto il quorum, cerca di ucciderlo
     * <li>Altrimenti fa nulla
     * </ol>
     * @return boolean Ritorna true se tutto è andato per il verso giusto. False
     * se il giocatore da uccidere non esiste
     */
    public function performActionNight() {
        $votes = $this->getVoteLupus();
        // se non è stato l'ultimo a votare, non fa nulla
        // esiste sempre almeno un voto di un lupo. Altrimenti la partita sarebbe 
        // terminata
        if ($votes[0]["id_user"] != $this->user->id_user)
            return true;
        
        $candidates = array();
        foreach ($votes as $vote)
            if (!isset ($candidates[$vote["vote"]]))
                $candidates[$vote["vote"]] = 1;
            else
                $candidates[$vote["vote"]]++;
        
        arsort($candidates);
        
        $num_votes = reset($candidates);
        $id_dead = key($candidates);
        $dead = User::fromIdUser($id_dead);
        
        // se il giocatore votato non esiste, c'è un bug nella votazione...
        if (!$dead)
            return false;
        
        // quorum
        if ($num_votes >= (int)(count($votes) * 0.5) + 1)
            $this->kill ($dead);
        
        return true;
    }

    /**
     * Ritornerà l'HTML dello splash all'arrivo al villaggio
     * @return string Stringa HTML da includere nella pagina
     */
    public function splash() {
        return "Sei un lupo...";
    }

}
