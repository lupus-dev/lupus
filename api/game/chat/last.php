<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere il timestamp dell'ultimo messaggio inviato
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
    $dest_user = User::fromUsername($_GET["user"]);
    if (!$dest_user)
        response (404, array(
            "error" => "L'utente destinatario non esiste",
            "code" => APIStatus::UserNotFound
        ));
    if (!$game->inGame($dest_user->id_user))
        response (404, array(
            "error" => "L'utente destinatario non appartiene a questa partita",
            "code" => APIStatus::ChatUserNotInGame
        ));
    $dest = $dest_user->id_user;
    if ($dest == $user->id_user)
        response (400, array(
            "error" => "Non puoi inviarti un messaggio",
            "code" => APIStatus::ChatInvalidUser
        ));
} else
    $dest = 0;

$last = Chat::getLastTimestamp($game, $user->id_user, $group, $dest);
$response = array(
    "timestamp" => $last,
    "code" => APIStatus::ChatSuccess
);

if (isset($_GET["after"])) {
    $timestamp = intval($_GET["after"]);
    $response["after"] = (int)Chat::getNumAfterTimestamp($game, $user->id_user, $group, $timestamp, $dest);
}

response(200, $response);