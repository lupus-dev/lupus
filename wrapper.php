<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

// inizializza tutto
require_once __DIR__ . "/core/init.php";

//$request = isset($_GET["request"]) ? $_GET["request"] : "";
$request = $_SERVER['REQUEST_URI'];
$request = substr($request, strlen($baseDir)+1);  // +1 perchè va aggiunto /

// espressione regolare per un nome breve
$shortName = "[a-zA-Z][a-zA-Z0-9]*";

// elenco dei percorsi riconosciuti:
// le chiavi sono delle espressioni regolari
// i valori:
//   - se terminano con .php verranno inclusi
//   - altrimenti ci sarà un redirect
// al primo match la valutazione si ferma
$paths = array(
    "/^index\/?$/" => "index/index.php",
    "/^login\/?$/" => "login/login.php",
    "/^signup\/?$/" => "signup/signup.php",
    "/^game\/?$/" => "game/game.php",
    "/^game\/($shortName)\/_new\/?$/" => "game/new_game/new_game.php",
    "/^game\/($shortName)\/($shortName)\/?$/" => "game/game_name/game_name.php",
    "/^room\/?$/" => "room/room.php",
    "/^room\/($shortName)\/?$/" => "room/room_name/room_name.php",
    "/^room\/_new\/?$/" => "room/new_room/new_room.php",
    // tutto quello che non è riconoscuto rimanda all'index
    "/.*/" => "index"
);

// questo pezzo di codice è stato tratto da:
// https://github.com/edomora97/registro
$matches = array();
foreach ($paths as $path => $dest) {
	if (preg_match($path, $request, $matches)) {
		if (endsWith($dest, ".php"))
			require __DIR__ . "/pages/$dest";
		else
			header("Location: $baseDir/$dest");
		break;
	}
}