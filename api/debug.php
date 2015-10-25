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

$sql = "SELECT 1 FROM game JOIN room ON game.id_room=room.id_room WHERE room.room_name='room' AND game.game_name='game';";
print_r(Database::query($sql));
