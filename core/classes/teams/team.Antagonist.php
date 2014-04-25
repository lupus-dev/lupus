<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta la squadra degli Antagonisti
 */
class Antagonist extends Team {
    
    public static $team_name = "antagonist";
    public static $name = "Antagonisti";
    public static $priority = 100;
    public static $team_code = 1;
    
    /**
     * Ritorna true se il numero di antagonisti vivi è maggiore o uguale al numero
     * di cittadini vivi. Eventuali altri ruoli vengono esclusi
     * @return boolean True se la partita è finita
     */
    public function checkWin() {
        $antag_alive = $this->getAliveTeam();
        $villages = new Villages($this->engine);
        $villa_alive = $villages->getAliveTeam();
        
        return count($antag_alive) >= count($villa_alive) && count($antag_alive) > 0;
    }

}