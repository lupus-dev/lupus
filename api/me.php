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

if (!$login) 
    response (200, array("info" => "Utente non connesso"));
else
    response (202, array(
        "info" => "Utente connesso",
        "username" => $user->username,
        "level" => $user->level
    ));