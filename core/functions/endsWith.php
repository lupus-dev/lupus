<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Verifica se $string termina con $end
 * @param string $string
 * @param string $end
 * @return bool True se $string termina con $end
 */
function endsWith($string, $end) {
	$l = strlen($end);
	return substr($string, -$l) == $end;
}