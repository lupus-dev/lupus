<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per modificare le specifiche di una partita
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

if (!isset($_GET["descr"]) || !isset($_GET["gen_info"]))
    response(400, array(
        "error" => "Specificare descr e gen_info in GET",
        "code" => APIStatus::SetupMissingParameter
    ));

$game_descr = $_GET["descr"];
$gen_info = $_GET["gen_info"];

if (!preg_match("/^$descr_name$/", $game_descr))
    response(400, array(
        "error" => "I parametri descr e gen_info non sono in un formato corretto",
        "code" => APIStatus::SetupMalformed
    ));
if (!isset($gen_info["gen_mode"]) ||
        !isset($gen_info["auto"]) || !isset($gen_info["auto"]["num_players"]) || !isset($gen_info["auto"]["roles"]) ||
        !isset($gen_info["manual"]) || !isset($gen_info["manual"]["roles"]))
    response(400, array(
        "error" => "I parametri descr e gen_info non sono in un formato corretto",
        "code" => APIStatus::SetupMalformed
    ));

$room = Room::fromRoomName($room_name);
if (!$room)
    response(400, array(
        "error" => "La stanza $room_name non esiste",
        "code" => APIStatus::RoomNotFound));
$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response(400, array(
        "error" => "La partita $room_name/$game_name non esiste",
        "code" => APIStatus::GameNotFound));

if ($room->id_admin != $user->id_user)
    response(401, array(
        "error" => "Non sei l'amministratore di questa stanza",
        "code" => APIStatus::SetupAccessDenied));

if ($game->status != GameStatus::Setup)
    response(401, array(
        "error" => "La partita non è in fase di setup",
        "code" => APIStatus::SetupNotInSetup));

$num_players = intval($gen_info["gen_mode"] == "auto" ?
                $gen_info["auto"]["num_players"] :
                array_sum($gen_info["manual"]["roles"]));
if ($num_players < RoleDispenser::MinPlayers || $num_players > 18)
    response(400, array(
        "error" => "Troppi o troppo pochi giocatori: devono essere presenti almeno "
            . RoleDispenser::MinPlayers . " e al più 18 giocatori",
        "code" => APIStatus::SetupInvalidNumPlayers
    ));

$res = $game->editGame($game_descr, $gen_info);

if (!$res)
    response(500, array(
        "error" => "Errore interno nel modificare la partita",
        "code" => APIStatus::FatalError
    ));
response(200, array(
    "game" => Game::makeResponse($game),
    "code" => APIStatus::SetupSuccess
));
