<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Rende la prima lettera della stringa maiuscola
 * @param string $string Stringa
 * @return string La stringa con la prima lettera maiuscola
 */
function firstUpper($string) {
    return strtoupper(substr($string, 0, 1)) . substr($string, 1);
}