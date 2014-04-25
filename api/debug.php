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

$lupo = new Lupo($user, $game);
$engine = new Engine($game);

$ant = new Antagonist($engine);
$vil = new Villages($engine);

print_r($engine->run());
echo "<br>";

var_dump($vil->checkWin());
var_dump($ant->checkWin());