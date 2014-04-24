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

// lista dei percorsi da redirigere
$apiPaths = array(
    // /login
    "/^login$/" => "login.php",
    // /login/(username)
    "/^login\/([a-zA-Z][a-zA-Z0-9]*)$/" => "login.php",
    // /logout
    "/^logout$/" => "logout.php",
    // /me
    "/^me$/" => "me.php",
    // /status
    "/^status$/" => "status.php"
);

// dentro apiMatches ci sono gli eventuali match della richiesta
$apiMatches = array();
foreach ($apiPaths as $path => $dest) {
    if (preg_match($path, $apiReq, $apiMatches)) {
        if (endsWith($dest, ".php"))
            require __DIR__ . "/$dest";
        else
            header("Location: $baseDir/$dest");
        break;
    }
}