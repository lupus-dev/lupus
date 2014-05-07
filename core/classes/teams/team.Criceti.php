<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class Criceti extends Team {
    
    public static $team_name = "criceti";
    public static $name = "Criceti";
    public static $priority = 50;
    public static $team_code = 2;
    
    /**
     * Ritorna true se la fazione dei criceti ha vinto: 
     * se i contadini o gli antagonisti hanno vinto e c'è ancora un criceto vivo.
     * False altrimenti
     * @return boolean True se i criceti vincono. False altrimenti
     */
    public function checkWin() {
        $antagonists = new Antagonist($this->engine);
        $villages = new Villages($this->engine);
        if ($antagonists->checkWin() || $villages->checkWin()) {
            $alive = $this->getAliveTeam();
            if (count($alive) > 0)
                return true;
        }
        return false;
    }
}