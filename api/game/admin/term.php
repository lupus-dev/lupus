<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per terminare una partita
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
        "code" => APIStatus::NotFound));
if ($room->id_admin != $user->id_user)
    response(401, array(
        "error" => "La stanza non appartiene all'utente",
        "code" => APIStatus::NotAuthorized));
$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response(400, array(
        "error" => "La partita $room_name/$game_name non esiste",
        "code" => APIStatus::NotFound));

if ($game->status != GameStatus::Running)
    response(422, array(
        "error" => "La partita non è in corso",
        "status" => $game->status,
        "code" => APIStatus::GameTermNotRunning));

$game->status(GameStatus::TermByAdmin);

response(200, array(
    "status" => $game->status,
    "code" => APIStatus::GameTerminated));
