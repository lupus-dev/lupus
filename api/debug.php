<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API di debug delle funzioni... qui potrebbe esserci di tutto...
 */

// vvvvvvv zona di SETUP dell'ambiente di debug
echo "Questo &egrave; il debug....<br><br>";
echo "<pre>";
Config::$log_level = LogLevel::Verbose;
// ^^^^^^^ 
$user = User::fromUsername("user3");
print_r(Game::getOpenGames($user));

exit;
$game = Game::fromRoomGameName("room", "game2");
$engine = new Engine($game);

$user2 = User::fromUsername("root");

$role = Role::fromUser($user, $engine);

Event::insertMediumAction($game, $user, $user2);
