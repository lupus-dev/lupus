<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per espellere un giocatore da una partita
 */
if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$room = Room::fromRoomName($room_name);
if (!$room)
    response(404, array(
        "error" => "La stanza cercata non esiste",
        "code" => APIStatus::RoomNotFound));
if ($room->id_admin != $user->id_user)
    response(401, array(
        "error" => "La stanza non appartiene all'utente",
        "code" => APIStatus::PlayerKickNotAuthorized));
$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response(400, array(
        "error" => "La partita $room_name/$game_name non esiste",
        "code" => APIStatus::GameNotFound));

if ($game->status != GameStatus::Running && $game->status != GameStatus::NotStarted)
    response(422, array(
        "error" => "La partita non è in uno stato valido",
        "status" => $game->status,
        "code" => APIStatus::PlayerKickNotValidState));

if (!isset($_POST["player"]))
    response(400, array(
        "error" => "Non è stato specificato il parametro 'player'",
        "code" => APIStatus::PlayerKickMissingParameter));

$username = $_POST["player"];

if (!$game->kickPlayer($user, $username))
    response(400, array(
        "status" => "Espulsione fallita",
        "code" => APIStatus::PlayerKickFailed));

response(200, array(
    "status" => "Giocatore espulso",
    "code" => APIStatus::PlayerKicked));
