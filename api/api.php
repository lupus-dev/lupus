<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../core/init.php";

$apiReq = $matches[1];

$apiPaths = array(
    "/^login$/" => "login/login.php",
    "/^logout$/" => "logout/logout.php",
    "/^version$/" => "version/version.php"
);

$apiMatches = array();
foreach ($apiPaths as $path => $dest) {
    if (preg_match($path, $apiReq, $matches)) {
        if (endsWith($dest, ".php"))
            require __DIR__ . "/$dest";
        else
            header("Location: $baseDir/$dest");
        break;
    }
}