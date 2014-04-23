<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Propone un redirect alla pagina specificata e termina lo script
 * @global string $baseDir
 * @param string $url URL di destinazione
 * @example redirect("index"); Effettua il redirect alla pagina iniziale del sito
 */
function redirect($url) {
    global $baseDir;
    header("Location: $baseDir/$url");
    exit();
}