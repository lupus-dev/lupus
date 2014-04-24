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
$shortName = "[a-zA-Z][a-zA-Z0-9]*";

// lista dei percorsi da redirigere
$apiPaths = array(
    "/^login$/" => "login.php",
    "/^login\/($shortName)$/" => "login.php",
    "/^logout$/" => "logout.php",
    "/^me$/" => "me.php",
    "/^user\/($shortName)$/" => "user.php",
    "/^room\/($shortName)$/" => "room.php",
    "/^game\/($shortName)\/($shortName)$/" => "game.php",
    "/^status$/" => "status.php",
    "/^new_room\/($shortName)$/" => "new_room.php",
    "/^new_game\/($shortName)\/($shortName)$/" => "new_game.php",
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