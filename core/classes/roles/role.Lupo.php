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
    public static $chat_groups = array(ChatGroup::Game, ChatGroup::Lupi);
    // i lupi non vengono mai scelti nella generazione
    public static $gen_probability = 0;
    public static $gen_number = 0;

    // memorizza il giocatore che deve essere ucciso. Evita una ricomputazione
    protected $voted = null;

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
        if ($this->roleStatus() != RoleStatus::Alive)
            return false;
        $vote = $this->getVote();
        // se l'utente non ha ancora votato la partita rimane in attesa
        if (is_bool($vote) && !$vote) {
            $alive = $this->engine->game->getAlive();
            $votable = array();
            foreach ($alive as $user)
                if ($this->getRole($user, $this->engine->game) != Lupo::$role_name)
                    $votable[] = $user->username;
            $pre = "<p>Vota chi sbranare!</p>";

            $lupi = $this->getOtherLupi();
            if (!$lupi || count($lupi) == 0)
                $pre .= "<p>Sei l'unico lupo vivo nella partita</p>";
            else
                $pre .= "<p>Gli altri lupi sono: " . implode(", ", $lupi) . "</p>";

            $votes = $this->getVoteLupus();
            if ($votes) {
                $pre .= "<p>Gli altri lupi hanno votato:</p><ul>";
                foreach ($votes as $vote)
                    $pre .= "<li>" . User::fromIdUser($vote["vote"])->username;
                $pre .= "</ul>";
            }
            return array(
                "votable" => $votable,
                "pre" => $pre
            );
        }
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
        
        $roles = Team::getRoles(RoleTeam::Antagonists);

        // genera ?,?,?,...,? da mettere nella query
        $queryInPart = str_repeat("?,", count($roles)-1) . "?";
        
        // cerca tutti i voti della parita e nel giorno che appartengono ai lupi
        $query = "SELECT id_user,vote 
                  FROM vote 
                  WHERE id_game=? AND day=? AND 
                  (SELECT role FROM player WHERE vote.id_user=player.id_user AND player.id_game=?) IN ($queryInPart) 
                  ORDER BY id_vote DESC";

        $params = [$id_game, $day, $id_game];
        for ($i = 0; $i < count($roles); $i++)
            $params[] = strtolower($roles[$i]);

        $res = Database::query($query, $params);
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
        // se l'utente è morto non agisce
        if ($this->getRoleStatus($this->engine->game, $this->user->id_user) != RoleStatus::Alive)
            return true;
        $votes = $this->getVoteLupus();
        // se non è stato l'ultimo a votare, non fa nulla
        // esiste sempre almeno un voto di un lupo. Altrimenti la partita sarebbe 
        // terminata
        if ($votes[0]["id_user"] != $this->user->id_user)
            return true;

        logEvent("L'utente {$this->user->username} è stato scelto per sbranare", LogLevel::Debug);

        $candidates = array();
        foreach ($votes as $vote)
            if (!isset($candidates[$vote["vote"]]))
                $candidates[$vote["vote"]] = 1;
            else
                $candidates[$vote["vote"]] ++;

        arsort($candidates);

        $num_votes = reset($candidates);
        $id_dead = key($candidates);
        $dead = User::fromIdUser($id_dead);

        // memorizzo l'utente votato
        $this->voted = $dead;
        
        // se il giocatore votato non esiste, c'è un bug nella votazione...
        if (!$dead) {
            logEvent("E' stato votato un giocatore inesistente ($id_dead x$num_votes)", LogLevel::Warning);
            return false;
        }

        // quorum
        if ($num_votes >= (int) (count($votes) * 0.5) + 1) {
            if ($this->kill($dead)) {
                logEvent("Il giocatore {$dead->username} è stato sbranato", LogLevel::Debug);
                Event::insertDeath($this->engine->game, $dead, "kill-lupo", $this->user->username);
            } else
                logEvent("Il giocatore {$dead->username} non è stato sbranato", LogLevel::Debug);
            // il lupo visita il giocatore sia se lo uccide, sia se non lo uccide
            $this->visit($dead);
        } else
            logEvent("Non è stato raggiunto il quorum per l'uccisione dal lupo", LogLevel::Debug);

        return true;
    }

    public function splash() {
        return "Sei un lupo";
    }

    /**
     * Verifica se l'utente votato esiste, è vivo e non è un lupo
     * @param string $username Utente votato
     * @return boolean True se il voto è valido. False altrimenti
     */
    public function checkVoteNight($username) {
        $normal = parent::checkVoteNight($username);
        if (!$normal)
            return false;
        $user = User::fromUsername($username);
        $role = Role::getRole($user, $this->engine->game);
        return $role != Lupo::$role_name;
    }

    /**
     * Ritorna la lista con gli username degli altri lupi vivi nella partita, il giocatore corrente
     * non è presente nella lista.
     * @return array|bool Array con gli username degli altri lupi. False in caso di errore
     */
    protected function getOtherLupi() {
        $id_game = $this->engine->game->id_game;
        $id_user = $this->user->id_user;
        $role = Lupo::$role_name;
        $status = RoleStatus::Alive;

        $query = "SELECT username
                  FROM player
                  JOIN user ON player.id_user = user.id_user
                  WHERE id_game=? AND user.id_user!=? AND role=? AND status=?";
        $res = Database::query($query, [$id_game, $id_user, $role, $status]);

        if (!$res)
            return false;

        $usernames = [];
        foreach ($res as $user)
            $usernames[] = $user["username"];

        return $usernames;
    }
}
