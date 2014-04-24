<?php

/* 
 * Registro Elettronico
 * GPLv2 - GNU PUBLIC LICENSE
 * Contrubutors:
 *   2013-2014 - Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Check if a string starts with an other string
 * @param string $string The string where check the start
 * @param string $start The checked start
 * @return boolean True if $string starts with $start. False otherwise
 */
function startsWith($string, $start) {
	$l = strlen($start);
	return substr($string, 0, $l) == $start;
}