<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per entrare in una parita
 */

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    response(400, array(
        "error" => "La partita $room_name/$game_name non esiste",
        "code" => APIStatus::GameNotFound));

if ($game->inGame($user->id_user))
    response(401, array(
        "error" => "Fai già parte di questa partita",
        "code" => APIStatus::JoinFailedAlreadyIn));

$gameStatus = $game->status;
if ($gameStatus != GameStatus::NotStarted)
    response(401, array(
        "error" => "La partita non accetta ingressi",
        "code" => APIStatus::JoinFailedGameClose));

if (count($game->players["players"]) + 1 > $game->players["num_players"])
    response(401, array(
        "error" => "La partita è già al completo",
        "code" => APIStatus::JoinFailedGameFull));

$game->joinGame($user);

$engine = new Engine($game);

// la votazione è riuscita, avvia il motore
$status = $engine->run();

// verifica lo stato della partita
if ($status == Engine::NextDay)
    response(201, array(
        "join" => "Ingresso riuscito",
        "status" => "Partita avviata",
        "game" => Game::makeResponse($game),
        "code" => APIStatus::JoinDoneGameStarted
    ));
if ($status == Engine::NeedVote)
    response(201, array(
        "join" => "Ingresso riuscito",
        "status" => "Partita in attesa",
        "game" => Game::makeResponse($game),
        "code" => APIStatus::JoinDoneGameWaiting
    ));
if ($status >= 500)
    response(500, array(
        "error" => "Un errore del server ha interrotto la partita",
        "code" => APIStatus::FatalError));

response(500, array(
    "error" => "Il server ha riportato un errore non riconosciuto",
    "code" => APIStatus::FatalError));
