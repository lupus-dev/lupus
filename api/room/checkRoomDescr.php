<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per verificare se il la descrizione della stanza è valida
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

if (!isset($_GET["room_descr"]))
    response(400, array(
        "error" => "Non è stato specificato il parametro room_descr",
        "code" => APIStatus::CheckRoomDescrMissingParameter
    ));

$room_descr = $_GET["room_descr"];

if (!preg_match("/^[a-zA-Z0-9][a-zA-Z0-9 ]{0,43}[a-zA-Z0-9]$/", $room_descr))
    response(200, array(
        "status" => false,
        "code" => APIStatus::CheckRoomDescrMalformed
    ));

response(200, array(
    "status" => true,
    "code" => APIStatus::CheckRoomDescrAccepted
));
