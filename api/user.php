<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per visualizzare le informazioni di un utente
 */

// lo username dell'utente deve essere in $apiMatches[1]
$username = $apiMatches[1];

// l'utente deve essere connesso
if (!$login)
    response(401, array("error" => "Utente non connesso"));

$reqUser = User::fromUsername($username);
// l'utente deve esistere
if (!$reqUser)
    response (404, array("error" => "Utente non trovato"));

response(202, array(
    "username" => $reqUser->username,
    "name" => $reqUser->name,
    "surname" => $reqUser->surname,
    "level" => $user->level
));
