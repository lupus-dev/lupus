<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere informazioni su una partita
 */

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response (404, array(
        "error" => "Partita non trovata",
        "code" => APIStatus::GameNotFound));

if (!$game->checkAuthorized($user))
    response(403, array(
        "error" => "Permessi insufficienti per accedere alla partita",
        "code" => APIStatus::AccessDenied));

response(202, array(
    "game" => Game::makeResponse($game),
    "code" => APIStatus::GameFound));
