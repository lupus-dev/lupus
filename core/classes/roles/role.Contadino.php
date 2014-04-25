<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Contadino
 */
class Contadino extends Role {
    
    public $role_name = "contadino";
    public $name = "Contadino";
    public $debug = false;
    public $enabled = true;
    public $priority = 10000;
    public $team = RoleTeam::Villages;
    public $mana = Mana::Good;
    
    /**
     * Ritornerà l'HTML dello splash all'arrivo al villaggio
     * @return string Stringa HTML da includere nella pagina
     */
    public function splash() {
        return "Sei un contadino...";
    }

}