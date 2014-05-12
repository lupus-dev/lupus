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

    /**
     * Formatta il giorno della partita in Giorno [n], Notte [n] ecc...
     * @param int $day Numero del giorno
     * @param boolean $includeNum True se vanno aggiunti anche i numeri dei 
     * giorni/notti
     * @return string Una stringa con la data formattata
     */
    public static function getNameFromDay($day, $includeNum = false) {
        $gameTime = GameTime::fromDay($day);
        switch ($gameTime) {
            case GameTime::Start:
                return "Arrivo al villaggio";
            case GameTime::Day:
                return "Giorno" . ($includeNum ? " " . ((int) ($day / 2) + 1) : "");
            case GameTime::Night;
                return "Notte" . ($includeNum ? " " . ((int) ($day / 2) + 1) : "");
            default:
                return "Unknown";
        }
    }

}
