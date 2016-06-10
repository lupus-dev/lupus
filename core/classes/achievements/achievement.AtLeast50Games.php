<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKGames.php";

class AtLeast50Games extends AtLeastKGames {

    public static $achievement_name = "AtLeast50Games";
    public static $name = "Esperto del mestiere";
    public static $description = "Gioca almeno 50 partite";
    public static $enabled = true;
    public static $difficulty = 12;
    protected $K = 50;

}
