<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/achievement.AtLeastKGames.php";

class AtLeast100Games extends AtLeastKGames {

    public static $achievement_name = "AtLeast100Games";
    public static $name = "Saggio del villaggio";
    public static $description = "Gioca almeno 100 partite";
    public static $enabled = true;
    public static $difficulty = 13;
    protected $K = 100;

}
