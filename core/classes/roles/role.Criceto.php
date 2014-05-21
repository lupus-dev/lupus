<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class Criceto extends Role {
    
    public static $role_name = "criceto";
    public static $name = "Criceto";
    public static $debug = false;
    public static $enabled = true;
    public static $priority = 10000;
    public static $team_name = RoleTeam::Criceti;
    public static $mana = Mana::Bad;
    // valore di riferimento di probabilità
    public static $gen_probability = 0.5;
    // i contadini non vengono generati in gruppo
    public static $gen_number = 1;

    public function splash() {
        return "Sei un criceto";
    }
}