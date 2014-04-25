<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta la squadra dei Cittadini
 */
class Villages extends Team {
    
    public static $team_name = "villages";
    public static $name = "Cittadini";
    public static $priority = 100;
    public static $team_code = 0;
    
    /**
     * Ritorna true se tutti gli antagonisti sono stati sconfitti. E c'è almeno
     * un cittadino vivo. False altrimenti
     * @return boolean True se la partita termina
     */
    public function checkWin() {
        $villages_alive = $this->getAliveTeam();
        $antagonist = new Antagonist($this->engine);
        $antagonist_alive = $antagonist->getAliveTeam();
        
        return count($antagonist_alive) == 0 && count($villages_alive) > 0;
    }
}