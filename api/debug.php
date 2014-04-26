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



$game = Game::fromRoomGameName("room", "game");
$user = User::fromUsername("user3");

print_r(Event::insertDeath($game, $user, "kill-lupo", "user1"));