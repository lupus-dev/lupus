<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per creare una nuova partita
 */
if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

if (!isset($_GET["descr"]))
    response(400, array(
        "error" => "Specificare descr in GET",
        "code" => APIStatus::NewGameMissingParameter));

$game_descr = $_GET["descr"];

if (!preg_match("/^$descr_name$/", $game_descr))
    response(400, array(
        "error" => "Il parametro descr non è in un formato corretto",
        "code" => APIStatus::NewGameMalformed
    ));

$room = Room::fromRoomName($room_name);
if (!$room)
    response(404, array(
        "error" => "La stanza cercata non esiste",
        "code" => APIStatus::RoomNotFound));
if ($room->id_admin != $user->id_user)
    response(401, array(
        "error" => "La stanza non appartiene all'utente",
        "code" => APIStatus::NewGameAccessDenied));

if (!$room->isAllTerminated())
    response(401, array(
        "error" => "C'è ancora una partita in corso in questa stanza",
        "code" => APIStatus::NewGameAlreadyRunning));

$existGame = Game::checkIfExists($room_name, $game_name);
if ($existGame)
    response(409, array(
        "error" => "Esiste già una partita in questa stanza di nome '$game_name'",
        "code" => APIStatus::NewGameAlreadyExists));

$level = Level::getLevel($user->level);
if (count($user->getActiveGame()) + 1 > $level->aviableGame)
    response(401, array(
        "error" => "L'utente ha finito le partite di cui può far parte",
        "code" => APIStatus::JoinFailedGamesEnded
    ));

$res = Game::createGame($room_name, $game_name, $game_descr, $user);

if (!$res)
    response(500, array(
        "error" => "Non è stato possibile creare la partita",
        "code" => APIStatus::FatalError));

response(201, array(
    "game" => Game::makeResponse($res),
    "code" => APIStatus::NewGameDone));
