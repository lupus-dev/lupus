<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere le informazioni di una stanza
 */

$room_name = $apiMatches[1];

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room = Room::fromRoomName($room_name);
if (!$room)
    response (404, array(
        "error" => "Stanza non trovata",
        "code" => APIStatus::RoomNotFound));

$admin = User::fromIdUser($room->id_admin);

if ($admin->id_user != $user->id_user && $room->private)
    response (401, array(
        "error" => "La stanza non è accessibile all'utente'",
        "code" => APIStatus::RoomAccessDenied));

response(202, array(
    "room" => Room::makeResponse($room),
    "code" => APIStatus::RoomFound));