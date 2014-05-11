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
if (!$role)
    response (401, array(
        "error" => "L'utente non fa parte della partita",
        "code" => APIStatus::ChatAccessDenied
    ));

$groups = $role::$chat_groups;
$groups[] = ChatGroup::User;

$group = ChatGroup::getChatGroup($group_name);
if (!in_array($group, $groups))
    response (401, array(
        "error" => "Il gruppo non è visibile all'utente",
        "code" => APIStatus::ChatAccessDenied
    ));

if ($group == ChatGroup::User) {
    if (!isset($_GET["user"]))
        response (400, array(
            "error" => "Non è stato specificato il parametro user",
            "code" => APIStatus::ChatMissingParameter
        ));            
    $dest = User::fromUsername($_GET["user"]);
    if (!$dest)
        response (404, array(
            "error" => "L'utente cercato non esiste",
            "code" => APIStatus::UserNotFound
        ));
    if (!$game->inGame($dest->id_user))
        response (401, array(
            "error" => "L'utente non appartiene alla partita",
            "code" => APIStatus::ChatAccessDenied
        ));
    $dest = $dest->id_user;
} else
    $dest = 0;

$messages = Chat::getGroupMessage($game, $user, $group, $dest);
$res = ChatMessage::makeResponseMultiple ($messages);

$chat_info = Chat::getUserChatInfo($game, $user);
if ($group == ChatGroup::User) 
    $chat_info["users"][$dest] = time();    
else
    $chat_info["groups"][$group] = time();
Chat::setUserChatInfo($game, $user, $chat_info);

response(200, array(
    "messages" => $res,
    "code" => APIStatus::ChatSuccess
));