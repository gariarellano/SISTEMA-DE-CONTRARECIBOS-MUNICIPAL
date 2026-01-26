<?php
/*
 *      index.php
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 * 
 */
session_start();
error_reporting(-1);
ini_set('display_errors',true);
date_default_timezone_set("America/Mexico_City");

include("nucleo/cargar.php");
print $MVC->procesar();
?>
