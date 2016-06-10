<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKGames.php";

class AtLeast20Games extends AtLeastKGames {

    public static $achievement_name = "AtLeast20Games";
    public static $name = "Iniziamo a ragionare...";
    public static $description = "Gioca almeno 20 partite";
    public static $enabled = true;
    public static $difficulty = 11;
    protected $K = 20;

}
