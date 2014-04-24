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
    
    public $role_name = "lupo";
    public $name = "Lupo";
    public $debug = false;
    public $enabled = true;
    public $priority = 100;
    public $team = Team::Antagonists;
    public $mana = Mana::Bad;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    public function needVote() {
        // un lupo morto non vota
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote)
            return "";
        return false;
    }

    public function performAction() {
        // verifica i voti degli altri lupi (si assume che tutti abbiano votato)
        // se è stato l'ultimo a votare (id_vote massimo)
        // uccidi il giocatore (se non è stato protetto)
        // @todo decidere se è il lupo a verificare se un giocatore non muore
        //       o se è colui che protegge ad evitare la sua morte <--
        return false;
    }

    public function splash() {
        // ritornare dell'HTML per il form per scegliere il bersaglio del voto
        return "";
    }

}
