<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Risponde ad una richiesta delle API. Stampa la risposta in json ed esce dallo
 * script
 * @param int $code Codice HTTP/1.0 della risposta
 * @param array $response Vettore con i dati della risposta
 */
function response($code, array $response) {
	$httpCode = response_code($code);
	header("HTTP/1.0 $code $httpCode");
	header("content-type: application/json");
	echo json_encode($response, JSON_PRETTY_PRINT);
	exit;
}