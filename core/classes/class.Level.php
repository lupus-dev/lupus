<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni di un livello
 */
class Level {
    /**
     * Nome del livello
     * @var string
     */
    public $name;
    /**
     * Numero di stanze creabili
     * @var int
     */
    public $aviableRoom;
    /**
     * Numero di stanze private creabili
     * @var int
     */
    public $privateRoom;
    /**
     * Numero di partite in cui giocare contemporaneamente
     * @var int
     */
    public $aviableGame;
    /**
     * Indica se le funzioni BETA sono disponibili
     * @var boolean
     */
    public $betaFeature;

    /**
     * Costruttore privato
     */
    private function __construct($name, $aviableRoom, $privateRoom, $aviableGame, $betaFeature) {
        $this->name = $name;
        $this->aviableRoom = $aviableRoom;
        $this->privateRoom = $privateRoom;
        $this->aviableGame = $aviableGame;
        $this->betaFeature = $betaFeature;
    }

    /**
     * Ottiene l'elenco dei livelli registrati
     * @return array Ritorna un vettore di \Level indicizzati per numero del 
     * livello
     */
    public static function getLevels() {
        $levels = array();
        $levels[1] = new Level("Neofita", 0, 0, 3, false);
        $levels[2] = new Level("Principiante", 1, 0, 5, false);
        $levels[3] = new Level("Gamer", 3, 1, 5, false);
        $levels[4] = new Level("Esperto", 5, 3, 5, false);
        $levels[5] = new Level("Maestro", 5, 5, 7, false);
        $levels[6] = new Level("Progamer", 10, 5, 10, false);
        $levels[7] = new Level("Stratega", 10, 10, 15, true);
        $levels[8] = new Level("Generale", 100, 10, 50, true);
        $levels[9] = new Level("Guru", 100, 100, 100, true);
        $levels[10] = new Level("GameMaster", 1000, 1000, 1000, true);
        return $levels;
    }
    /**
     * Ritorna il livello con il numero indicato
     * @param int $level Numero del livello
     * @return boolean|\Level Il livello con il numero specificato, false se non
     * esiste
     */
    public static function getLevel($level) {
        $levels = Level::getLevels();
        if (isset($levels[$level]))
            return $levels[$level];
        return false;
    }
}
