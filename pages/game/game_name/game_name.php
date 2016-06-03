<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login)
    redirect("login");

$room_name = $matches[1];
$game_name = $matches[2];

$room = Room::fromRoomName($room_name);
$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    require __DIR__ . "/not_found.php";
else if (!$game->checkAuthorized($user))
    redirect("index");
else if ($game->status == GameStatus::Setup)
    require __DIR__ . "/setup.php";
else if ($game->status == GameStatus::NotStarted)
    require __DIR__ . "/join.php";
else if ($game->status == GameStatus::Running)
    require __DIR__ . "/running.php";
else 
    require __DIR__ . "/end.php";
