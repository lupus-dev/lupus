<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKGames.php";

class AtLeast5Games extends AtLeastKGames {

    public static $achievement_name = "AtLeast5Games";
    public static $name = "Appena iniziato";
    public static $description = "Gioca almeno 5 partite";
    public static $enabled = true;
    public static $difficulty = 10;
    protected $K = 5;

}
