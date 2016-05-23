<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Pastore
 */
class Pastore extends Role {

    public static $role_name = "pastore";
    public static $name = "Pastore";
    public static $debug = true;
    public static $enabled = true;
    public static $priority = 50;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $gen_probability = 0.5;
    public static $gen_number = 1;

    public function __construct($user, $game) {
        parent::__construct($user, $game);
    }

    /**
     * Verifica se il pastore deve votare. Se è morto o se ha già votato ritorna 
     * false. Altrimenti ritorna il form per votare
     * @return boolean|string
     */
    public function needVoteNight() {
        // un pastore morto non vota
        if ($this->roleStatus() != RoleStatus::Alive)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            // l'utente può votare solo 'Si' o 'No'
            return array(
                "votable" => array("Si", "No"),
                "pre" => "<p>Vuoi offrire un sacrificio?</p>"
            );
        }
        return false;
    }

    /**
     * Esegue l'azione associata al pastore: se ha ancora almeno un sacrificio
     * e ha scelto di usarlo, lo usa. In questo caso viene protetto dai lupi
     * @return boolean Ritorna true se tutto è andato per il verso giusto.
     */
    public function performActionNight() {
        // se l'utente è morto non agisce
        if ($this->getRoleStatus($this->engine->game, $this->user->id_user) != RoleStatus::Alive)
            return true;
        $vote = $this->getVote();
        // se il voto è zero ha scelto di non usare il sacrificio
        if ($vote == 0)
            return true;
        $data = $this->getData();
        // se i dati non sono presenti o sono corrotti li rigenera
        if (!$data || !isset($data["left"])) {
            // il numero di sacrifici rimasti varia in [1,3]
            $data = array("left" => rand() % 3 + 1);
            $this->setData($data);
        }
        // usa il sacrificio
        $data["left"] --;
        $this->setData($data);
        // se aveva finito i sacrifici disponibili, l'utente muore...
        if ($data["left"] < 0) {
            $this->kill($this->user);
            Event::insertDeath($this->engine->game, $this->user, "suicidio-pastore", "no-one");
            return true;
        }
        // altrimenti il giocatore viene protetto
        $this->protectUserFromRole($this->user->id_user, Lupo::$role_name);
        return true;
    }

    /**
     * Verifica se il voto è valido (Si o No)
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        // un pastore può votare solo "Si" o "No"
        if ($username == "Si" || $username == "No")
            return true;
        return false;
    }

    /**
     * Effettua una votazione. Il metodo è diverso dall'originale perchè l'utente
     * non vota il nome di un'altro utente. Può votare solo Si o No
     * @param string $voted Stringa contenente il voto dell'utente (Si o No)
     * @return boolean True se la votazione è riuscita, false altrimenti
     */
    public function vote($voted) {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;
        $day = $this->engine->game->day;

        if ($voted == "Si")
            $vote = -1;
        else
            $vote = 0;

        $query = "INSERT INTO vote (id_game,id_user,vote,day) VALUE (?, ?, ?, ?)";
        $res = Database::query($query, [$id_game, $id_user, $vote, $day]);
        if (!$res) {
            logEvent("Impossibile compiere la votazione di $id_user => $voted. id_game=$id_game", LogLevel::Warning);
            return false;
        }
        return true;
    }

    public function splash() {
        return "Sei un pastore";
    }

}
