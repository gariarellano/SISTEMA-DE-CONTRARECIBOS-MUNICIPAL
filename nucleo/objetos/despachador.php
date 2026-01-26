<?php
/*
 *      despachador.php
 *      
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */

class Despachador{
	function __construct(){
		Registro::Escribir("Metodos",array());
		$this->obtUrl();
	}
	
	function procesar(){
		//Registro::Escribir('ArcCache',CACHE.substr(md5(registro::Leer('control')."_".Registro::Leer('funcion')),0,15).".php");
		/*if($this->cache()){
			return gzinflate(Registro::Leer('cache'));
		}else{*/
			if(Registro::Leer("LANG"))
				include_once(IDIOMA . Registro::Leer("IDIOMA") .".php");
			
			if(!class_exists(Registro::Leer('control'),true)){
				Registro::Escribir('Error',array("control"=>Registro::Leer('control'),"funcion"=>Registro::Leer('funcion')));
				
				if( Registro::Verificar('PEC') && in_array(substr(Registro::Leer('control'),0,-7),Registro::Leer('PEC')))
					$tmpfuncion='enconstruccion';
				else
					$tmpfuncion='notfound';

				$control = new erroresControl();
				Registro::Escribir('Pag',$control->procesar($tmpfuncion));
			}else{
				$c=Registro::Leer('control');
				$control = new $c();
				Registro::Escribir('Pag',$control->procesar(Registro::Leer('funcion'),explode("/",Registro::Leer('parametros'))));
			}
			
			//$this->crearCache();
			return Registro::Leer('Pag');
		//}
	}
	
	private function obtUrl(){
		$url=isset($_GET['url'])?$_GET['url']:"";
		$url = trim($url,"/");
		
		unset($_GET);
		$defineUrl="";
		Registro::Escribir("bdrAdmin",false);
		
		if(Registro::Verificar("Page")){
			$ui=explode("/",$url,2);
			foreach(Registro::Leer("Page") as $cve =>$valor){
				if($ui[0]==strtolower($cve)){
					Registro::Escribir("FPLANTILLA",$valor);
					$nurl=URLSITIO.strtolower($cve);
					$defineUrl=$nurl."/";
					$url=isset($ui[1])?$ui[1]:"";
					break;
				}
			}
		}
		
		if(Registro::Leer("LANG")){
			$uidioma=explode("/",$url,2);
			foreach(Registro::Leer("IDIOMAS") as $cve =>$valor){
				if($uidioma[0]==strtolower($cve)){
					Registro::Escribir("IDIOMA",$cve);
					if($defineUrl=="") $nurl=URLSITIO.strtolower($cve);
					else $nurl=strtolower($cve);
					$defineUrl.=$nurl."/";
					$url=isset($uidioma[1])?$uidioma[1]:"";
					break;
				}
			}
		}
		
		if(Registro::Verificar("Admin")){
			$ui=explode("/",$url,2);
			if(strtolower($ui[0])==strtolower(Registro::Leer("Admin"))){
				$url=isset($ui[1])?$ui[1]:"";
				$defineUrl.=$ui[0]."/";
				Registro::Escribir("FPLANTILLA","admin");
				Registro::Escribir("bdrAdmin",true);
			}
		}
		
		define("URL",$defineUrl?$defineUrl:URLSITIO);
				
		Registro::Escribir("GetUrl",$url);
		
		if($url=="") $url="/";
		$url=trim(urldecode($url));
		$u=explode("/",$url,2);
		
		if(count(Registro::Leer("Ruta"))){
			foreach(Registro::Leer("Ruta") as $cve => $valor){
				$express=false;
				$c=explode("/",$cve,2);
				if(isset($c[1]) && $c[1]=="*"){
					$cve=$c[0]."/(.+)";
					$express=true;	
				}
				
				if($express){
					if(preg_match("#".$cve."#is",$url)){
						$url=implode("/",$valor) ."/". $u[1];
						break;
					}
				}else if(isset($u[1]) && $u[1]!=''){
					$u2=explode("/",$u[1],2);
					if(strtolower($cve)==trim(strtolower($u[0]."/".$u2[0]),"/\\")){
						$url=implode("/",$valor) ."/". (isset($u2[1])?$u2[1]:"");
						break;
					}
				}else if($cve==$url){
					$url=implode("/",$valor);
					break;
				}
			}
		}
		
		$url=explode("/",trim($url,"/\\"),3);
		if(count($url)>0){
			Registro::Escribir('control',($url[0]!=""?strtolower($url[0]):"index")."Control");
			
			if(Registro::Leer("bdrAdmin")) Registro::Escribir('funcion',"admin_".(isset($url[1])?$url[1]:"index"));
			else Registro::Escribir('funcion',isset($url[1])?$url[1]:"index");
				
			Registro::Escribir('parametros',isset($url[2])?$url[2]:'');
		}
	}
	
	private function cache(){
		if(strtolower(Registro::Leer("CACHEACTIVO"))=="si" ){
			if (file_exists(Registro::Leer('ArcCache'))){
				$creado=fileatime(Registro::Leer('ArcCache'));
				$ahora=strtotime("-".Registro::Leer("CACHETIEMPO")." seconds");
				
				if($ahora <= $creado){
					Registro::Escribir('cache',file_get_contents(Registro::Leer('ArcCache')));
					return true;
				}else{
					unlink(Registro::Leer('ArcCache'));
				}
			}
		}
		
		return false;
	}
	
	private function crearCache(){
		$gestor = fopen(Registro::Leer('ArcCache'), "w");
		fwrite($gestor,gzdeflate(Registro::Leer('Pag')));
		fclose($gestor);
	}
}

?>
