<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per visualizzare le informazioni dell'utente connesso
 */

if ($login)
    $apiMatches[1] = $user->username;
else
    $apiMatches[1] = "";

require __DIR__ . "/user.php";
