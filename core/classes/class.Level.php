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
     * Percorso dove trovare il file con i metadati dei livelli
     */
    const LEVEL_PATH = __DIR__ . "/../metadata/data.levels.json";

    /**
     * Karma guadagnato per partita giocata dall'utente
     */
    const KARMA_PER_JOIN = 10;
    /**
     * Karma guadagnato per partita vinta
     */
    const KARMA_PER_WIN = 10;
    /**
     * Karma guadagnato per partita creata
     */
    const KARMA_PER_CREATED = 5;


    /**
     * Cache dei livelli presenti nel file LEVEL_PATH
     * @var array|null
     */
    private static $levelsCache;

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
     * Quantità minima di karma necessario per entrare in questo livello
     * @var int
     */
    public $requiredKarma;

    /**
     * Costruttore privato
     */
    private function __construct($data) {
        $this->name = $data['name'];
        $this->aviableRoom = $data['availableRoom'];
        $this->privateRoom = $data['privateRoom'];
        $this->aviableGame = $data['concurrentGame'];
        $this->betaFeature = $data['betaFeature'];
        $this->requiredKarma = $data['requiredKarma'];
    }

    /**
     * Ottiene l'elenco dei livelli registrati
     * @return array Ritorna un vettore di \Level indicizzati per numero del 
     * livello
     */
    public static function getLevels() {
        if (Level::$levelsCache) return Level::$levelsCache;

        $levels = array();
        $json = file_get_contents(Level::LEVEL_PATH);

        $i = 1;
        foreach (json_decode($json, true) as $l)
            $levels[$i++] = new Level($l);

        return Level::$levelsCache = $levels;
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

    /**
     * Check if the user can level up, if yes level up the user...
     * @param User $user User to check
     * @return boolean True if the user has been leveled up, false otherwise
     */
    public static function checkLevelAdvance($user) {
        $level = $user->level;
        $karma = $user->karma;
        $levels = Level::getLevels();

        for ($i = $level+1; $i <= count($levels); $i++)
            if ($karma >= $levels[$i]->requiredKarma)
                $level = $i;

        if ($level <= $user->level)
            return false;

        $sql = "UPDATE user SET level=? WHERE id_user=?";
        $res = Database::query($sql, [$level, $user->id_user]);
        if (!$res) return false;

        logEvent("L'utente $user->username passa dal livello $user->level al livello $level", LogLevel::Debug);

        $user->level = $level;
        return true;
    }
}
