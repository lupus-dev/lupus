<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere i messaggi della chat in un gruppo
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];
$group_name = $apiMatches[3];

$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    response (404, array(
        "error" => "Partita non trovata",
        "code" => APIStatus::GameNotFound));

$role = firstUpper(Role::getRole($user, $game));
$groups = $role::$chat_groups;
$groups[] = ChatGroup::User;

$group = ChatGroup::getChatGroup($group_name);
if (!in_array($group, $groups))
    response (401, array(
        "error" => "Il gruppo non è visibile all'utente",
        "code" => APIStatus::ChatAccessDenied
    ));

$messages = Chat::getGroupMessage($game, $user, $group);
$res = ChatMessage::makeResponseMultiple ($messages);

response(200, array(
    "messages" => $res,
    "code" => APIStatus::ChatSuccess
));