<?php
/*
 *      Error.class.php
 *      
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */
 
class ErroresControl extends Control{
	var $nombre='Errores';

	function notfound(){
		$this->agrVariable("titulo","404 Pagina no encontrada");
	}
	
	function enconstruccion(){
		$this->agrVariable("titulo","503 Pagina en construccion");	
	}
}
?>
