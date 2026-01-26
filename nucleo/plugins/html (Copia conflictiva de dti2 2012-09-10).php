<?php
/*
 *      html.php
 *      Plugin para el uso de ajax
 *      Copyright 2011 LUIS FERNANDO JAIMES BENITEZ <luis@gmail.com>
 *      
 */
class Html extends Plugins{
	private $archivo=array();
	private $metas=array(0=>array("name","generator","LFramePHP 1.0 - rsolache"));
	
	function Meta($meta,$contentenido=null){
		if($meta=="utf8"){
			array_unshift($this->metas,array("http-equiv","Content-Type","text/html; charset=utf-8"));
			
			if(Registro::Leer("SEO")){
				array_push($this->metas,array("name","keywords",Registro::Leer("SEO_KEYWORD")));
				array_push($this->metas,array("name","description",Registro::Leer("SEO_DESCRIPTION")));
			}
		}else
			array_push($this->metas,array("name",$meta,$contentenido));
	}
	
	function LinkCss($archivo,$type="text/css",$rel="stylesheet",$bandera=true){
		
		if($bandera && $archivo != "favicon")
			$archivo="plantillas/".Registro::Leer("FPLANTILLA")."/css/".$archivo.".css";
		else if($archivo=="favicon"){
			$archivo="plantillas/".Registro::Leer("FPLANTILLA")."/".$archivo.(($type=="png")?".png":".ico");
			$type=($type=="png")?"image/png":"image/icon";
			
			array_unshift($this->archivo,array($archivo,"icon",$type));
			$rel="shortcut icon";
		}
		
		array_unshift($this->archivo,array($archivo,$rel,$type));
	}
	
	function Link($url,$titulo,$parametros=array()){
		$elementos=array();
		
		if(!isset($parametros['prefijo']) || $parametros['prefijo'])
			$url=URL.$url;
		
		unset($parametros['prefijo']);
		
		foreach($parametros as $cve => $valor){
			$elementos[]=$cve."='".$valor."'";
		}
			
		return "<a href='".str_replace("'","\"",$url)."' ".join(" ",$elementos).">".$titulo."</a>";
	}
	
	function Mostrar(){
		$resultado="";
		foreach($this->metas as $cve => $valor){
			$resultado.=vsprintf("<meta %s='%s' content='%s' />",$valor);
		}
		
		$resultado.="<base href=".URLSITIO." />";
		
		foreach($this->archivo as $cve => $valor){
			$resultado.=vsprintf("<link  href='%s' rel='%s' type='%s'/>",$valor);
		}	
		
		echo $resultado;
	}
	
	function img($archivo,$option=array()){
		$back=false;
		if(isset($option['fondo'])){
			$back=true;
			unset($option['fondo']);
		}
		
		$parametros=array();
		foreach($option as $cve => $valor){
			$parametros[]=$cve ."='".$valor."'";
		}
		
		$ruta=$archivo;
		
		foreach(Registro::Leer("IMG") as $cve => $valor){
			if(file_exists("plantillas/".Registro::Leer("FPLANTILLA")."/".$valor."/".$archivo)){
				$ruta="plantillas/".Registro::Leer("FPLANTILLA")."/".$valor."/".$archivo;
				break;
			}else if(file_exists($valor."/".$archivo)){
				$ruta=$valor."/".$archivo;
				break;
			} 
		}
		
		if(!$back)
			return "<img src='".$ruta."' ".join(" ",$parametros)." />";
		else
			return $ruta;
	}
	
	function PathImg($archivo){
		return $this->img($archivo,array("fondo"=>true));
	}
}
