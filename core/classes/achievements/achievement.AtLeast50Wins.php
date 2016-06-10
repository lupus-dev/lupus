<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKWins.php";

class AtLeast50Wins extends AtLeastKWins {

    public static $achievement_name = "AtLeast50Wins";
    public static $name = "Pericolo pubblico";
    public static $description = "Vinci almeno 50 partite";
    public static $enabled = true;
    public static $difficulty = 22;
    protected $K = 50;

}
