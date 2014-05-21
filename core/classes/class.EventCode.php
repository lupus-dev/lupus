<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene i codici degli eventi
 */
class EventCode {
    /**
     * Il gioco è iniziato
     */
    const GameStart = 0;
    /**
     * Un giocatore è morto
     */
    const Death = 1;
    /**
     * Il medium ha visto il mana di un giocatore
     */
    const MediumAction = 2;
    /**
     * Il veggente ha visto il mana di un giocatore
     */
    const VeggenteAction = 3;
    /**
     * Il paparazzo ha guardato qualcuno 
     */
    const PaparazzoAction = 4;
    /**
     * Il becchino ha resuscitato qualcuno
     */
    const BecchinoAction = 5;
    
    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }
}
