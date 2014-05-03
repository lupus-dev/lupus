<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../core/init.php";

// estrae la richiesta dall'url
$baseApiDir = $baseDir . "/api/";
$apiReqUri = $_SERVER['REQUEST_URI'];
$apiReq = substr($apiReqUri, strlen($baseApiDir));
$apiReq = ($temp = strstr($apiReq, "?", true)) ? $temp : $apiReq;

// espressione regolare per un nome breve
$shortName = "[a-zA-Z][a-zA-Z0-9]{0,9}";

// lista dei percorsi da redirigere
$apiPaths = array(
    "/^login$/" => "auth/login.php",
    "/^login\/($shortName)$/" => "auth/login.php",
    "/^logout$/" => "auth/logout.php",
    "/^me$/" => "user/me.php",
    "/^user\/($shortName)$/" => "user/user.php",
    "/^room\/($shortName)$/" => "room/room.php",
    "/^game\/($shortName)\/($shortName)$/" => "game/game.php",
    "/^game\/($shortName)\/($shortName)\/vote$/" => "game/vote.php",
    "/^game\/($shortName)\/($shortName)\/join$/" => "game/join.php",
    "/^game\/($shortName)\/($shortName)\/start$/" => "game/start.php",
    "/^status$/" => "status.php",
    "/^new_room\/($shortName)$/" => "room/new_room.php",
    "/^new_game\/($shortName)\/($shortName)$/" => "game/new_game.php",
    "/^checkRoomName$/" => "room/checkRoomName.php",
    "/^checkRoomDescr$/" => "room/checkRoomDescr.php",
    "/^checkGameName$/" => "game/checkGameName.php",
    "/^checkGameDescr$/" => "game/checkGameDescr.php",
    "/^debug$/" => "debug.php",    
);

// dentro apiMatches ci sono gli eventuali match della richiesta
$apiMatches = array();
foreach ($apiPaths as $path => $dest) {
    if (preg_match($path, $apiReq, $apiMatches)) {
        if (endsWith($dest, ".php"))
            require __DIR__ . "/$dest";
        else
            header("Location: $baseDir/$dest");
        return;
    }
}

response(400, array("error" => "$apiReq non è una richiesta valida"));