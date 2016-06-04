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
        "code" => APIStatus::NotFound));

if ($game->inGame($user->id_user))
    response(401, array(
        "error" => "Fai già parte di questa partita",
        "code" => APIStatus::JoinFailedAlreadyIn));

$gameStatus = $game->status;
if ($gameStatus != GameStatus::NotStarted)
    response(401, array(
        "error" => "La partita non accetta ingressi",
        "code" => APIStatus::JoinFailedGameClose));

if ($game->getNumPlayers() + 1 > $game->num_players)
    response(401, array(
        "error" => "La partita è già al completo",
        "code" => APIStatus::JoinFailedGameFull));

$level = Level::getLevel($user->level);
if (count($user->getActiveGame()) + 1 > $level->aviableGame)
    response (401, array(        
        "error" => "L'utente ha finito le partite di cui può far parte",
        "code" => APIStatus::JoinFailedGamesEnded));

if (!$game->checkAuthorized($user))
    response(403, array(
        "error" => "Permessi insufficienti per accedere a questa partita",
        "code" => APIStatus::AccessDenied));

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
if ($status == Engine::EndGame)
    response(201, array(
        "join" => "Ingresso riuscito",
        "status" => "Partita terminata",
        "game" => Game::makeResponse($game),
        "code" => APIStatus::GameTerminated
    ));
if ($status >= 500)
    response(500, array(
        "error" => "Un errore del server ha interrotto la partita",
        "code" => APIStatus::FatalError));

response(500, array(
    "error" => "Il server ha riportato un errore non riconosciuto",
    "code" => APIStatus::FatalError));
