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
$paths = array(
    "/^index$/" => "index.php",
    "/^login$/" => "login.php",
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