<?php

/* 
 * Registro Elettronico
 * GPLv2 - GNU PUBLIC LICENSE
 * Contrubutors:
 *   2013-2014 - Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$pageScripts = array();

/**
 * Stampa la stringa HTML per includere uno script solo se non è già presente 
 * nella pagina
 * @param string $script Lo script da includere nella cartella /js/
 */
function insertScript($script) {
	global $pageScripts;
	global $baseDir;
	if (!is_array($pageScripts)) $pageScripts = array();
	if (in_array($script, $pageScripts))
		return;
	$pageScripts[] = $script;
	echo "<script src=\"$baseDir/js/$script\"></script>";
}