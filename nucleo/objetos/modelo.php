<?php
/*
 *      modelo.php
 *      
 *      Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *      
 */
class Modelo extends Basedatos{
	
	public static function Instancia(){
		if(self::$instancia==null){
			$c = __CLASS__;
			self::$instancia=new $c();
		}
		
		return self::$instancia;
	}
	
	
	public function ListarTodo(){
		return $this->Consultar(strtolower($this->nombre),"*",array(),true);
	}
	
	public function ListarCampos($campos,$parametros=array(),$indice=true){
		return $this->Consultar(strtolower($this->nombre),$campos,$parametros,true,$indice);
	}
	
	public function Avanzado($query,$indice=true,$recuperar=false){
		return $this->EjecutarSql($query,$recuperar,$indice);
	}
	
	public function Guardar($datos){
		return $this->Insertar(strtolower($this->nombre),$datos);
	}
}
?>
