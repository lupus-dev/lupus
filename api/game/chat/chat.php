<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere le chat della partita
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    response (404, array(
        "error" => "Partita non trovata",
        "code" => APIStatus::NotFound));

$role = firstUpper(Role::getRole($user, $game));
if (!$role)
    response (401, array(
        "error" => "L'utente non fa parte della partita",
        "code" => APIStatus::AccessDenied
    ));

$groups = $role::$chat_groups;

$res = array();

foreach ($groups as $group)
    $res[] = ChatGroup::getChatName ($group);
$res[] = "User";

response(200, array(
    "chat" => $res,
    "code" => APIStatus::Done
));
