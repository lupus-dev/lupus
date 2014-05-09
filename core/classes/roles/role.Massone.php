<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class Massone extends Role {
    
    public static $role_name = "massone";
    public static $name = "Massone";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 10000;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    public static $chat_groups = array(ChatGroup::Game, ChatGroup::Massoni);
    public static $gen_probability = 0.5;
    // i massoni vengono generati a coppie
    public static $gen_number = 2;
    
    /**
     * Ritornerà l'HTML dello splash all'arrivo al villaggio
     * @return string Stringa HTML da includere nella pagina
     */
    public function splash() {
        return "Sei un massone...";
    }
}