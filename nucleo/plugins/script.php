<?php
class Script extends Plugins{

	function file($archivo,$parametros=array()){
		if(!file_exists(SCRIPT.$archivo.".js"))
			$this->vista->msg[]="El plugins <i>".$archivo."</i> no se encontran en: <code>".SCRIPT.$archivo.".js</code>";
		else{
			"plantillas/".Registro::Leer("FPLANTILLA")."/js/".
			
		}
	}	
}
?>
