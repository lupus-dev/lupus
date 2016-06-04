<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere li numero di messaggi successivi ad un istante di ogni gruppo
 */

function getChatUsersInfo($game, $user, $dest, $after, $min) {
    $num_after = (int) Chat::getNumAfterTimestamp($game, $user->id_user, ChatGroup::User, $after, $dest->id_user);
    if ($min)
        return array("after" => $num_after);
    
    $last = (int) Chat::getLastTimestamp($game, $user->id_user, ChatGroup::User, $dest->id_user);
    
    return array(        
        "last" => $last,
        "after" => $num_after
    );
}

function getChatGroupInfo($game, $user, $group, $after, $min) {
    $num_after = (int) Chat::getNumAfterTimestamp($game, $user->id_user, $group, $after);
    if ($min)
        return array("after" => $num_after);
    
    $last = (int) Chat::getLastTimestamp($game, $user->id_user, $group);

    return array(
        "last" => $last,
        "after" => $num_after
    );
}

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    response(404, array(
        "error" => "Partita non trovata",
        "code" => APIStatus::NotFound));

$role = firstUpper(Role::getRole($user, $game));
if (!$role)
    response(401, array(
        "error" => "L'utente non fa parte della partita",
        "code" => APIStatus::AccessDenied
    ));

$min = isset($_GET["min"]);

$res = array();

$chat_info = Chat::getUserChatInfo($game, $user);

foreach ($chat_info["groups"] as $group => $time) {
    $group_name = ChatGroup::getChatName($group);
    $res["groups"][$group_name] = getChatGroupInfo($game, $user, $group, $time, $min);
}
foreach ($chat_info["users"] as $id_user => $time) {
    $dest = User::fromIdUser($id_user);
    $res["users"][$dest->username] = getChatUsersInfo($game, $user, $dest, $time, $min);
}
$res["code"] = APIStatus::Done;

response(200, $res, $min);
