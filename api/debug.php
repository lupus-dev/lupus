<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

echo "Questo &egrave; il debug....<br><br>";
echo "<pre>";

$game = Game::fromRoomGameName("room", "game");
$user = User::fromUsername("root");

$engine = new Engine($game);

print_r($engine->run());