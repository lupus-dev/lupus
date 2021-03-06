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
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));
if (!isset($_GET["descr"]))
    response(400, array(
        "error" => "Specificare una descrizione",
        "code" => APIStatus::MissingParameter));

$room_name = $apiMatches[1];
$room_descr = $_GET["descr"];
$private = isset($_GET["private"]) ? intval($_GET["private"]) : 0;

if (!preg_match("/^$descr_name$/", $room_descr))
    response (400, array(
        "error" => "Il parametro descr non è in un formato corretto",
        "code" => APIStatus::MalformedParameter));

$level = Level::getLevel($user->level);
if (!$level)
    response (500, array(
        "error" => "L'utente ha un livello non riconosciuto",
        "code" => APIStatus::FatalError));

$numPublicRooms = count($user->getPublicRoom());
$numPrivateRooms = count($user->getPrivateRoom());

if ($numPublicRooms+$numPrivateRooms+1 > $level->aviableRoom)
    response (403, array(
        "error" => "L'utente ha esaurito il numero di stanze disponibili",
        "code" => APIStatus::NewRoomRoomsEnded));
if ($private && $numPrivateRooms+1 > $level->privateRoom)
    response (403, array(
        "error" => "L'utente ha esaurito il numero di stanze private disponibili",
        "code" => APIStatus::NewRoomPrivateRoomsEnded));
if ($private != RoomPrivate::Open && $private != RoomPrivate::LinkOnly && $private != RoomPrivate::ACL)
    response(400, array(
        "error" => "Il parametro private non è nel formato corretto",
        "code" => APIStatus::MalformedParameter));

$existRoom = Room::checkIfExists($room_name);
if ($existRoom)
    response (409, array(
        "error" => "Esiste già una stanza di nome '$room_name'",
        "code" => APIStatus::NewRoomAlreadyExists));

$res = Room::createRoom($room_name, $room_descr, $user->id_user, $private);

if (!$res)
    response (500, array(
        "error" => "Non è stato possibile creare la stanza",
        "code" => APIStatus::FatalError));

response(201, array(
    "room" => Room::makeResponse($res),
    "code" => APIStatus::Done));
