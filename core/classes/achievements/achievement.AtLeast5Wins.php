<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKWins.php";

class AtLeast5Wins extends AtLeastKWins {

    public static $achievement_name = "AtLeast5Wins";
    public static $name = "Primi successi";
    public static $description = "Vinci almeno 5 partite";
    public static $enabled = true;
    public static $difficulty = 20;
    protected $K = 5;

}
