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
        "code" => APIStatus::GameNotFound));

$role = firstUpper(Role::getRole($user, $game));
$groups = $role::$chat_groups;

$res = array();

foreach ($groups as $group)
    $res[] = ChatGroup::getChatName ($group);

response(200, array(
    "chat" => $res,
    "code" => APIStatus::ChatSuccess        
));