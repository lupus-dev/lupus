<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

if (!isset($_GET["game_descr"]))
    response(400, array(
        "error" => "Non è stato specificato il parametro game_descr",
        "code" => APIStatus::MissingParameter
    ));

$game_descr = $_GET["game_descr"];

if (!preg_match("/^$descr_name$/", $game_descr))
    response(200, array(
        "status" => false,
        "code" => APIStatus::MalformedParameter
    ));

response(200, array(
    "status" => true,
    "code" => APIStatus::CheckGameDescrAccepted
));
