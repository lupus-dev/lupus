<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene i possibili valori di tempo in una partita
 */
class GameTime {
    /**
     * La partita è appena iniziata e i giocatori stanno entrando nel villaggio
     */
    const Start = 0;
    /**
     * E' giorno
     */
    const Day = 1;
    /**
     * E' notte
     */
    const Night = 2;
    
    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }
    
    /**
     * Crea un nuovo \GameTime in base al giorno della partita
     * @param int $day Giorno nella partita
     * @return \GameTime Tempo della partita
     */
    public static function fromDay($day) {
        if ($day == 0)
            return GameTime::Start;
        return ($day % 2 == 0) ? GameTime::Day : GameTime::Night;
    }
}