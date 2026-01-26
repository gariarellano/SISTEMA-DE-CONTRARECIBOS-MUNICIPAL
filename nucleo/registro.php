<?php
/*
 *      registro.php
 *      
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */
class Registro {
	static $__objectos = array();
	private static $instancia=null;
	
	private function __construct(){}
	
	public static function Instancia(){
		if(self::$instancia==null){
			$c = __CLASS__;
			self::$instancia=new $c();
		}
		
		return self::$instancia;
	}
	
	public static function Verificar($cve) {
		return isset(self::$__objectos[$cve]);
	}
	
	public static function Leer($cve) {
		if (isset(self::$__objectos[$cve]) ) {
			return self::$__objectos[$cve];
		}
		
		return null;
	}
	
	public static function Escribir($cve, $val, $bandera=false){
		if(!$bandera)
			self::$__objectos[$cve]=$val;
		else{
			if(self::Verificar($cve))
				self::$__objectos[$cve].=$val;
			else
				self::Escribir($cve,$val);
		}
	}
	
	public static function Ruta($url, $destino){
		self::$__objectos["Ruta"][$url]=$destino;
	}
	
	public static function Pages($Page,$Plantilla){
		self::$__objectos["Page"][$Page]=$Plantilla;
	}
	
	public static function Borrar($cve) {
		unset(self::$__objectos[$cve]);
	}
}
?>
