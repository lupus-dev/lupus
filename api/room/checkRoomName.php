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

if (!isset($_GET["room_name"]))
    response(400, array(
        "error" => "Non è stato specificato il parametro room_name",
        "code" => APIStatus::CheckRoomNameMissingParameter
    ));

$room_name = $_GET["room_name"];

if (!preg_match("/^$shortName$/", $room_name))
    response(200, array(
        "status" => false,
        "code" => APIStatus::CheckRoomNameMalformed
    ));

$room = Room::fromRoomName($room_name);
if ($room)
    response(200, array(
        "status" => false,
        "code" => APIStatus::CheckRoomNameExisting
    ));

response(200, array(
    "status" => true,
    "code" => APIStatus::CheckRoomNameAccepted
));
