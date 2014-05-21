<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


/**
 * Classe che rappresenta il ruolo Assassino
 */
class Assassino extends Role {

    public static $role_name = "assassino";
    public static $name = "Assassino";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 10;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Bad;
    public static $gen_probability = 0.5;
    public static $gen_number = 1;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    /**
     * Verifica se l'assassino deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {
        if ($this->roleStatus() == RoleStatus::Dead)
            return false;
        $data = $this->getData();
        // se i dati del ruolo non sono salvati o sono danneggiati
        if (!$data || !isset($data["killed"])) {
            // crea i dati e li salva
            $data = array("killed" => false);
            $this->setData($data);
        // se l'utente ha già ucciso non vota più
        } else if ($data["killed"])
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $alive = $this->engine->game->getAlive();
            $votable = array();
            foreach ($alive as $user)
                if ($user->id_user != $this->user->id_user)
                    $votable[] = $user->username;    
            $votable = array_merge(array("(nessuno)"), $votable);
            return array(
                "votable" => $votable,
                "pre" => "<p>Vota chi assassinare!</p>"
            );
        }
        return false;
    }

    /**
     * Esegue l'azione associata all'assassino, inserisce negli eventi della partita
     * l'assassinio
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
        // aggiungere assassinio!
        Event::insertDeath($this->engine->game, $voted, "kill-assassino", $this->user->username);
        // uccide e visita il giocatore
        $this->kill($voted);
        $this->visit($voted);
        // marca l'uccisione, l'assassino non potrà più uccidere
        $this->setData(array("killed" => true));
        return true;
    }

    public function splash() {
        return "Sei un assassino";
    }

    /**
     * Verifica se il voto dell'utente è valido
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        $data = $this->getData();
        // se i dati del ruolo non sono salvati o sono danneggiati
        if (!$data || !isset($data["killed"])) {
            // crea i dati e li salva
            $data = array("killed" => false);
            $this->setData($data);
        // se l'utente ha già ucciso non vota più
        } else if ($data["killed"])
            return false;
        if ($username == "(nessuno)")
            return true;
        $user = User::fromUsername($username);
        if (!$user) return false;
        $status = Role::getRoleStatus($this->engine->game, $user->id_user);
        if ($status == RoleStatus::Dead)
            return false;
        return $username != $this->user->username;
    }

    /**
     * Effettua la votazione di un personaggio. L'assassino può votare '(nessuno)',
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
