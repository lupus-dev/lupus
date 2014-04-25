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

$engine = new Engine($game);
$engine->run();