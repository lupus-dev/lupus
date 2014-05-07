<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class Sindaco extends Role {
    
    public static $role_name = "sindaco";
    public static $name = "Sindaco";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 10000;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $gen_probability = 0.5;
    public static $gen_number = 1;
    
    /**
     * Ritornerà l'HTML dello splash all'arrivo al villaggio
     * @return string Stringa HTML da includere nella pagina
     */
    public function splash() {
        return "Sei un massone...";
    }
    /**
     * Verifica se il sindaco deve votare di giorno. Il sindaco viene anche 
     * protetto dalla messa al rogo
     * @return boolean True se il sindaco deve ancora votare, false altrimenti
     */
    public function needVoteDay() {
        // il sindaco è protetto dalla messa al rogo
        $this->protectUserFromAll($this->user->id_user);
        return parent::needVoteDay();        
    }
}