<?php
/*
 * Control.php
 * * Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 * */
class Control{
    var $modelo;
    var $Vista;
    var $lang;
    var $usuario;
    var $Conectores=array();
    var $Conexion="";
    var $Session=array();
    var $nombre;
    // ¡NUEVO! Declaración explícita de la propiedad $Datos
    var $Datos; 
    
    function __construct(){
        if(isset($this->nombre) && class_exists($this->nombre,true))
            $this->modelo=new $this->nombre($this->Conexion);
        else{
            $this->modelo=Modelo::Instancia();
        }
        
        $this->Vista=Vista::Instancia();
    }
    
    function __toString(){
        return get_class($this);
    }
    
    function procesar($funcion,$parametros=array()){
        // Esta es la línea 33 que causaba el aviso.
        // Al declarar $Datos arriba, ya no es una propiedad dinámica.
        $this->Datos=$_POST; 
        $AntesDe = (Registro::Leer("bdrAdmin")?"admin_":"")."antesDe";
        $DepuesDe = (Registro::Leer("bdrAdmin")?"admin_":"")."DespuesDe";
        
        if(isset($_SESSION[Registro::Leer("SESSION")])){
            $this->Session=$_SESSION[Registro::Leer("SESSION")];
            unset($_SESSION[Registro::Leer("SESSION")]);
        }
        unset($_POST);
        
        $ControlPrevio=null;
        $this->agrVariable("titulo",$this->nombre);
        
        if(isset($_SESSION['msg'])){
            $this->Vista->msg=$_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        
        if(class_exists("PrevioControl",true)){
            $ControlPrevio=new previoControl();
            $ControlPrevio->nombre=$this->nombre;
            $ControlPrevio->Conexion=$this->Conexion;
            $ControlPrevio->Session=$this->Session;
            
            if(is_callable(array($ControlPrevio,$AntesDe)))
                call_user_func_array(array($ControlPrevio,$AntesDe),$parametros);
                
            $this->lang=$ControlPrevio->lang;
        }
        
        if(is_callable(array($this,$AntesDe)))
            call_user_func_array(array($this,$AntesDe),$parametros);
        
        if(is_callable(array($this,$funcion))){
            call_user_func_array(array($this,$funcion),$parametros);
        }else
            $this->Msg("La funcion: ".$funcion." no esta defina en la clase: ".$this);
        
        
        if(is_callable(array($this,$DepuesDe)))
            call_user_func_array(array($this,$DepuesDe),$parametros);
            
        if($ControlPrevio != null){ 
            if(is_callable(array($ControlPrevio,$DepuesDe)))
                call_user_func_array(array($ControlPrevio,$DepuesDe),$parametros);
        }
        
        
        $_SESSION[Registro::Leer("SESSION")] = $this->Session;
        $this->Vista->conectores=array_merge($this->Vista->conectores,$this->Conectores);        
        $this->Vista->asigna_variables(array("LOGDB"=>$this->modelo->MostrarLog()));
        return $this->Vista->imprimir($this->nombre,$funcion); 
    }
    
    function redireccionar($ruta=false){
        $_SESSION['msg']=$this->Vista->msg;
        
        if($ruta==false){
            echo "<script> location.href='".$_SERVER['HTTP_REFERER']."';</script>";
        }else if(!is_array($ruta)){
            echo "<script> location.href='".URLSITIO.$ruta."';</script>";
        }
    }
    
    function agrVariable($cve,$dato){
        $this->Vista->asigna_variables(array($cve=>$dato));
    }
    
    function Msg($cadena){
        $this->Vista->msg[]=$cadena;
    }
    
    function usar_ajax(){
        $this->agrVariable("Ajax","activo");
    }
    
    function factorizar(&$array){
        foreach($array as $cve => $valor)
            $this->agrVariable($cve,$valor);
    }
    
    function tpl($archivo){
        $this->Vista->tpl($archivo);
    }
}
