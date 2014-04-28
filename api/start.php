<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per far iniziare una partita
 */

if (!$login)
    response(401, array("error" => "Utente non connesso"));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$room = Room::fromRoomName($room_name);
if (!$room)
    response(400, array("error" => "La stanza $room_name non esiste"));
$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response(400, array("error" => "La partita $room_name/$game_name non esiste"));

if ($room->id_admin != $user->id_user)
    response (401, array("error" => "Non sei l'amministratore di questa stanza"));

if ($game->status != GameStatus::Setup)
    response (401, array("error" => "La partita non è in fase di setup"));

$game->startGame();

response(200, Game::makeResponse($game));