<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKWins.php";

class AtLeast100Wins extends AtLeastKWins {

    public static $achievement_name = "AtLeast100Wins";
    public static $name = "Stratega professionista";
    public static $description = "Vinci almeno 100 partite";
    public static $enabled = true;
    public static $difficulty = 23;
    protected $K = 100;

}
