<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

// inizializza tutto
require_once __DIR__ . "/core/init.php";

$request = isset($_GET["request"]) ? $_GET["request"] : "";

// elenco dei percorsi riconosciuti:
// le chiavi sono delle espressioni regolari
// i valori:
//   - se terminano con .php verranno inclusi
//   - altrimenti ci sarà un redirect
// al primo match la valutazione si ferma
$paths = array(
    "/^api\/(.*)/" => "../api/api.php",
    "/^index$/" => "index.php",
    "/^login$/" => "login.php",
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