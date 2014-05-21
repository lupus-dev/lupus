<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Becchino
 */
class Becchino extends Role {

    public static $role_name = "becchino";
    public static $name = "Becchino";
    public static $debug = true;
    public static $enabled = true;
    public static $priority = 10000;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $gen_probability = 0.5;
    public static $gen_number = 1;

    public function splash() {
        return "Sei un becchino...";
    }

    /**
     * Verifica se il becchino deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $data = $this->getData();
        // se i dati del ruolo non sono salvati o sono danneggiati
        if (!$data || !isset($data["acted"])) {
            // crea i dati e li salva
            $data = array("acted" => false);
            $this->setData($data);
            // se l'utente ha già resuscitato non vota più
        } else if ($data["acted"])
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $dead = $this->engine->game->getDead();
            // se non ci sono morti il becchino non vota
            if (!$dead)
                return false;
            $votable = array();
            foreach ($dead as $user)
                $votable[] = $user->username;
            $votable = array_merge(array("(nessuno)"), $votable);
            return array(
                "votable" => $votable,
                "pre" => "<p>Vota chi resuscitare!</p>"
            );
        }
        return false;
    }

    /**
     * Esegue l'azione associata al becchino, inserisce negli eventi della partita
     * la resurrezione
     * @return boolean Ritorna true se tutto è andato per il verso giusto. False
     * se il giocatore da resuscitare non esiste
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
        Event::insertBecchinoAction($this->engine->game, $this->user, $voted);
        // resuscita e visita il giocatore        
        $alive = RoleStatus::Alive;
        $id_game = $this->engine->game->id_game;
        $query = "UPDATE player SET status=$alive WHERE id_game=$id_game AND id_user=$vote";
        $res = Database::query($query);
        if (!$res)
            return false;
        $this->visit($voted);
        // marca l'azione, il becchino non potrà più resuscitare
        $this->setData(array("acted" => true));
        return true;
    }

    /**
     * Verifica se il voto dell'utente è valido
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        $data = $this->getData();
        // se i dati del ruolo non sono salvati o sono danneggiati
        if (!$data || !isset($data["acted"])) {
            // crea i dati e li salva
            $data = array("acted" => false);
            $this->setData($data);
            // se l'utente ha già resuscitato non vota più
        } else if ($data["acted"])
            return false;
        if ($username == "(nessuno)")
            return true;
        $user = User::fromUsername($username);
        if (!$user)
            return false;
        $status = Role::getRoleStatus($this->engine->game, $user->id_user);
        if ($status == RoleStatus::Alive)
            return false;
        return $username != $this->user->username;
    }

    /**
     * Effettua la votazione di un personaggio. Il becchino può votare '(nessuno)',
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
