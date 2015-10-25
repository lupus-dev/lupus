<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per verificare se il nome breve fornito è valido
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

if (!isset($_GET["room_name"]) || !isset($_GET["game_name"]))
    response(400, array(
        "error" => "Non è stato specificato il parametro room_name e game_name",
        "code" => APIStatus::CheckGameNameMissingParameter
    ));

$room_name = $_GET["room_name"];
$game_name = $_GET["game_name"];

if (!preg_match("/^$shortName$/", $room_name) || !preg_match("/^$shortName$/", $game_name))
   response(200, array(
        "status" => false,
        "code" => APIStatus::CheckGameNameMalformed
    ));

$room = Room::checkIfExists($room_name);
if (!$room)
    response(200, array(
        "status" => false,
        "code" => APIStatus::CheckGameNameNotFound
    ));

$game = Game::checkIfExists($room_name, $game_name);
if ($game) 
    response (200, array(
        "status" => false,
        "code" => APIStatus::CheckGameNameExisting
    ));

response(200, array(
    "status" => true,
    "code" => APIStatus::CheckGameNameAccepted
));
