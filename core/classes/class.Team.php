<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe astratta da cui derivano le squadre
 */
abstract class Team {

    /**
     * Nome breve della squadra
     * @var string
     */
    public static $team_name;

    /**
     * Nome completo della squadra
     * @var string
     */
    public static $name;

    /**
     * Priorità di controllo della vittoria della squadra. Valori inferiori 
     * indicano una priorità maggiore
     * @var int
     */
    public static $priority;

    /**
     * Codice identificativo della squadra. Necessario per essere identificata
     * negli stati delle partite. Deve essere strettamente inferiore a 100
     * @var int
     */
    public static $team_code;

    /**
     * Motore del gioco a cui è associata la squadra
     * @var type 
     */
    protected $engine;

    /**
     * Costruttore di \Team
     * @param \Engine $engine Motore del gioco a cui è associata la squadra
     */
    public function __construct($engine) {
        $this->engine = $engine;
    }

    /**
     * Verifica se questa squadra ha vinto e la partita deve terminare
     * @return boolean True se la squadra ha vinto e la partita deve terminare. 
     * False altrimenti 
     */
    public abstract function checkWin();

    /**
     * Cerca tutti i giocatori della squadra nella partita
     * @return boolean|array Ritorna un vettore con gli identificativi degli 
     * utenti della squadra
     */
    public function getAllTeam() {
        $roles = $this->engine->roles;
        $team_name = $this->getTeamName();
        $users = array();

        foreach ($roles as $role_name => $role_users) {
            $team = $role_name::$team_name;
            if ($team == $team_name)
                $users = array_merge($users, $role_users);
        }
        return $users;
    }

    /**
     * Cerca tutti i giocatori vivi della squadra
     * @return boolean|array Ritorna un vettore con gli identificativi degli 
     * utenti vivi della squadra
     */
    public function getAliveTeam() {
        $team = $this->getAllTeam();
        if (!$team)
            return false;

        $alive = array();

        foreach ($team as $id_user)
            if (Role::getRoleStatus($this->engine->game, $id_user) == RoleStatus::Alive)
                $alive[] = $id_user;
        return $alive;
    }

    /**
     * Ottiene il nome della squadra
     * @return string Il nome della squadra
     */
    public function getTeamName() {
        $class_name = get_class($this);
        return $class_name::$team_name;
    }

    /**
     * Ottiene una lista dei nomi delle classi delle squadre presenti nella cartella
     * @return boolean|array Un vettore di stringhe, false se si verifica un errore
     */
    public static function getAllTeams() {
        $dir = __DIR__ . "/teams/";
        $files = scandir($dir);
        if (!$files) {
            logEvent("La cartella delle squadre non è accessibile", LogLevel::Error);
            return false;
        }
        $teams = array();

        foreach ($files as $file) {
            $matches = array();
            // seleziona dalla cartella solo i file che rispettano il formato corretto
            if (preg_match("/team\.([a-zA-Z0-9]+)\.php/", $file, $matches)) {
                $team = $matches[1];
                if (class_exists($team) && in_array("Team", class_parents($team)))
                    $teams[] = $matches[1];
                else
                    logEvent("Nella cartella delle squadre c'è un file che non codifica una squadra valida ($file)", LogLevel::Notice);
            } else if (!startsWith($file, "."))
                logEvent("Nella cartella delle squadre è presente un file ($file) con nome non valido", LogLevel::Notice);
        }
        return $teams;
    }
    /**
     * Ottiene il nome della classe con il codice specificato
     * @param int $team_code Codice della squadra
     * @return boolean|string Il nome della classe della squadra. False se non
     * esiste
     */
    public static function fromTeamCode($team_code) {
        $teams = Team::getAllTeams();
        if (!$teams)
            return false;
        foreach ($teams as $team)
            if ($team::$team_code == $team_code)
                return $team;
        return false;
    }
    
    /**
     * Confronta due squadre per ordinarle in base alla loro priorità
     * @param \Team $teamA 
     * @param \Team $teamB
     * @return int Ritorna il valore del contronto delle priorità dei ruoli
     */
    static function cmpTeam($teamA, $teamB) {        
        $priA = $teamA::$priority;
        $priB = $teamB::$priority;
        if ($priA == $priB)
            return 0;
        return ($priA < $priB) ? -1 : 1;
    }
}
