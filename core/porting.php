<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * Qui verranno messi i porting delle funzioni/costanti che rendono lo script 
 * più retrocompatibile possibile
 */

// se non è definito JSON_PRETTY_PRINT lo imposta a 0 (valore nullo)
if (!defined("JSON_PRETTY_PRINT"))
    define ("JSON_PRETTY_PRINT", 0);