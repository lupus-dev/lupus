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
    response(401, array("error" => "Utente non connesso"));

$game = Game::fromRoomGameName($room_name, $game_name);
if (!$game)
    response (404, array("error" => "Partita non trovata"));

response(202, array(
    "room_name" => $room_name,
    "game_name" => $game_name,
    "day" => $game->day,
    "status" => $game->status,
    "game_descr" => $game->game_descr
));