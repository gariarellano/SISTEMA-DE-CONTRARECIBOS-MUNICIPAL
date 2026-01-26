<?php
/*
 *      ajax.php
 *      Plugin para el uso de ajax
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */
class Ajax extends Plugins{
	function click($destino,$text,$tipo="link"){
		$texto="";
		if($tipo=="button"){
			$texto.="<button id='btn".str_replace(" ","",ucwords($text))."'>".ucwords($text)."</button>";
		}
			
		return $texto;
	}
	
	function AjaxLink($url){
		return URL.$url;;
	}
}
