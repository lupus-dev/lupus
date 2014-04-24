<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per creare una nuova stanza
 */
if (!$login)
    response(401, array("error" => "Utente non connesso"));
if (!isset($_GET["descr"]))
    response(400, array("error" => "Specificare una descrizione"));

$room_name = $apiMatches[1];
$room_descr = $_GET["descr"];
$private = isset($_GET["private"]);

$level = Level::getLevel($user->level);
if (!$level)
    response (500, array("error" => "L'utente ha un livello non riconosciuto"));

$numPublicRooms = count($user->getPublicRoom());
$numPrivateRooms = count($user->getPrivateRoom());

if ($numPublicRooms+$numPrivateRooms+1 > $level->aviableRoom)
    response (403, array("error" => "L'utente ha esaurito il numero di stanze disponibili"));
if ($private && $numPrivateRooms+1 > $level->privateRoom)
    response (403, array("error" => "L'utente ha esaurito il numero di stanze private disponibili"));

$existRoom = Room::fromRoomName($room_name);
if ($existRoom)
    response (409, array("error" => "Esiste già una stanza di nome '$room_name'"));

$res = Room::createRoom($room_name, $room_descr, $user->id_user, $private);

if (!$res)
    response (500, array("error" => "Non è stato possibile creare la stanza"));

response(201, Room::makeResponse($res));