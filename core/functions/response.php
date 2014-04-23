<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function response($code, array $response) {
	$httpCode = response_code($code);
	header("HTTP/1.0 $code $httpCode");
	header("content-type: application/json");
	echo json_encode($response, JSON_PRETTY_PRINT);
	exit;
}