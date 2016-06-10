<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKWins.php";

class AtLeast20Wins extends AtLeastKWins {

    public static $achievement_name = "AtLeast20Wins";
    public static $name = "Hai capito le regole";
    public static $description = "Vinci almeno 20 partite";
    public static $enabled = true;
    public static $difficulty = 21;
    protected $K = 20;

}
