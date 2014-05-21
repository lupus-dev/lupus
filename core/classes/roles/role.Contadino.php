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
    
    public static $role_name = "contadino";
    public static $name = "Contadino";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 10000;
    public static $team_name = RoleTeam::Villages;
    public static $mana = Mana::Good;
    // valore di riferimento di probabilità
    public static $gen_probability = 10;
    // i contadini non vengono generati in gruppo
    public static $gen_number = 1;
    
    public function splash() {
        return "Sei un contadino";
    }

}