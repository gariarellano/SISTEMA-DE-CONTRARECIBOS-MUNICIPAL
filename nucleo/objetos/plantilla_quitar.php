<?php
/*
 *      plantilla.php
 *      
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */
class Plantilla {
	protected $datos=array();
	var $msg=array();
	var $conectores=array("Form","Javascript","Script");
	protected $plantilla;
 	var $registro;
	private static $instancia;
	
	private function __construct(){
		$this->registro=Registro::Instancia();
		
		if(file_exists(PLANTILLAS.FPLANTILLA.DS.'index.tpl')){
			$this->plantilla = PLANTILLAS.FPLANTILLA.DS.'index.tpl';
		}else{
			trigger_error("La plantilla no existe");
		}
	}
	
	public static function Instancia(){ 
      if (  !self::$instancia instanceof self){ 
         self::$instancia = new self; 
      } 
	  
      return self::$instancia; 
   }
	
	private function Conector(){
		foreach($this->conectores as $cve => $valor){
			$valor=strtolower($valor);
			$valor=ucwords($valor);
			echo $valor;
			
			try{
				$this->datos[$valor]=new $valor();
			}catch(Exception $e){
				$this->msg[]="El plugin ".strtoupper($valor)." no existe";
			}
		}
	}
	
	private function ErrorMsg(){
		$ErrorMsg="";
		if(count($this->msg)>0){
			$ErrorMsg.="<div style='clear:both;background:#000; color:#fff;'><pre><ul>";
			
			foreach($this->msg as $cve => $valor){
				$ErrorMsg.="<li>".$valor."</li>";
			}
			$ErrorMsg.="</pre></ul></div>";
		}
		
		return $ErrorMsg;
	}

	function imprimir($control,$archivo){
		$pagina=PAG.strtolower($control).DS.$archivo.".html";
		$body='';
		
		$this->Conector();
		
		while (list($cve, $valor) = each($this->datos)) {
			$$cve = $valor;
		}
		
		if(!file_exists($pagina))
			$this->msg[]="Debe crear el archivo: ".$pagina;
		else{
			ob_start();
			include($pagina);
			$body=ob_get_contents();
			ob_end_clean();
		}
		
		preg_match_all("/<script>(.+?)<\/script>/si",$body,$script);
		$body=preg_replace("#<script>(.+?)</script>#is"," ",$body);
		
		$this->registro['script']=join("\n",$script[1]);
		
		//$ErrorMsg=$this->ErrorMsg();
		$ErrorMsg.='<pre>Sin valor</pre>';
		ob_start();
		include($this->plantilla);
		$HTML=ob_get_contents();
		ob_end_clean();

		return $HTML;
	}
}
?>
