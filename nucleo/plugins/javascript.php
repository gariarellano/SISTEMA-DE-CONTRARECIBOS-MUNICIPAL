<?php
class Javascript extends Plugins{
	private $archivo=array();
	
	function JS($archivo,$primero=false){
		if($primero)
			array_unshift($this->archivo,"plantillas/".Registro::Leer("FPLANTILLA")."/js/".$archivo);
		else
			array_push($this->archivo,"plantillas/".Registro::Leer("FPLANTILLA")."/js/".$archivo);
	}
	
	function Script($archivo){
		array_push($this->archivo,"script/".$archivo);
	}
	
	
	function Mostrar($bdiv=false){
		$texto=$bdiv?"<div id='javascript'>":"";
		
		foreach($this->archivo as $cve => $valor){
			$texto.=sprintf($this->tags["javascript"],$valor);	
		}
		
		$texto.=$this->MostrarScript();
		echo $texto.($bdiv?"</div>":"");
	}
	
	function MostrarScript(){
		if(Registro::Leer('script'))
			return "<script type='text/javascript'>".Registro::Leer('script')."</script>";
		else
			return "";
	}
}
?>
