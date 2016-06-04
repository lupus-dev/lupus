<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per inviare un messaggio in chat
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
        "code" => APIStatus::NotFound));

$role = firstUpper(Role::getRole($user, $game));
$groups = $role::$chat_groups;
$groups[] = ChatGroup::User;

$group = ChatGroup::getChatGroup($group_name);
if (!in_array($group, $groups))
    response (401, array(
        "error" => "Il gruppo non è visibile all'utente",
        "code" => APIStatus::AccessDenied));

if (!isset($_GET["text"]))
    response (400, array(
        "error" => "Non è stato specificato il parametro text",
        "code" => APIStatus::MissingParameter));

$text = $_GET["text"];

if ($group != ChatGroup::User)
    $dest = 0;
else {
    if (!isset($_GET["dest"]))
        response (400, array(
            "error" => "Non è stato specificato il parametro dest",
            "code" => APIStatus::MissingParameter));
    $dest_user = User::fromUsername($_GET["dest"]);
    if (!$dest_user)
        response (404, array(
            "error" => "L'utente destinatario non esiste",
            "code" => APIStatus::NotFound));
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
}

$res = Chat::sendMessage($game, $user->id_user, $dest, $group, $text);

if (!$res)
    response (500, array(
        "error" => "Non è stato possibile inviare il messaggio",
        "code" => APIStatus::FatalError
    ));

response(201, array(
    "ok" => "Messaggio spedito!",
    "code" => APIStatus::Done
));
