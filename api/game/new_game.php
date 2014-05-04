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
if (!isset($_GET["descr"]) || !isset($_GET["num_players"]))
    response(400, array(
        "error" => "Specificare una descrizione e il numero di giocatori",
        "code" => APIStatus::NewGameMissingParameter));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game_descr = $_GET["descr"];
$num_players = intval($_GET["num_players"]);

if (!preg_match("/^$descr_name$/", $game_descr) || intval($num_players) == 0)
    response (400, array(
        "error" => "I parametri game_name e game_descr non sono in un formato corretto",
        "code" => APIStatus::NewGameMalformed
    ));

$room = Room::fromRoomName($room_name);
if (!$room)
    response (404, array(
        "error" => "La stanza cercata non esiste",
        "code" => APIStatus::RoomNotFound));
if ($room->id_admin != $user->id_user)
    response (401, array(
        "error" => "La stanza non appartiene all'utente",
        "code" => APIStatus::NewGameAccessDenied));

if (!$room->isAllTerminated())
    response (401, array(
        "error" => "C'è ancora una partita in corso in questa stanza",
        "code" => APIStatus::NewGameAlreadyRunning));

$existGame = Game::fromRoomGameName($room_name, $game_name);
if ($existGame)
    response (409, array(
        "error" => "Esiste già una partita in questa stanza di nome '$game_name'",
        "code" => APIStatus::NewGameAlreadyExists));

if ($num_players < RoleDispenser::MinPlayers)
    response (400, array(
        "error" => "Il numero di giocatori è insufficiente",
        "code" => APIStatus::NewGameNotEnouthPlayers));

$res = Game::createGame($room_name, $game_name, $game_descr, $num_players);

if (!$res)
    response (500, array(
        "error" => "Non è stato possibile creare la partita",
        "code" => APIStatus::FatalError));

response(201, array(
    "game" => Game::makeResponse($res),
    "code" => APIStatus::NewGameDone));