<?php
/*
 * Vista.php
 * * Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 * */

class Vista{
    protected $datos=array();
    var $msg=array();
    var $conectores=array("Form","Javascript","Html","Extension");
    protected $plantilla;
    private static $instancia;
    var $tpl="index";
    // ¡NUEVO! Declaraciones explícitas para las propiedades dinámicas
    var $Form;
    var $Javascript;
    var $Html;
    var $Extension;
    
    private function __construct(){     
        if(file_exists(PLANTILLAS.Registro::Leer("FPLANTILLA").DS.'index.tpl')){
            $this->plantilla = PLANTILLAS.Registro::Leer("FPLANTILLA").DS.'index.tpl';
        }else{
            $this->msg[]="La plantilla no existe";
        }
    }
    
    public static function Instancia(){ 
      if ( !self::$instancia instanceof self){ 
          self::$instancia = new self; 
      } 
      
      return self::$instancia; 
    }
    
    private function Conector(){
        foreach($this->conectores as $cve => $valor){
            $valor=strtolower($valor);
            $valor=ucwords($valor);
            
            if(class_exists($valor,true)){
                // Esta es la línea que causaba el aviso (línea 39 en tu archivo original).
                // Ahora, al declarar $Form, $Javascript, etc., ya no son dinámicas.
                $this->{$valor}=new $valor();
            }else
                $this->msg[]="El plugin ".strtoupper($valor)." no existe";
        }
    }
    
    private function ErrorMsg(){
        $ErrorMsg="";
        if(count($this->msg)>0){
            foreach($this->msg as $cve => $valor){
                $ErrorMsg.=$valor."<br/>";
            }
        }
        
        return $ErrorMsg;
    }

    function imprimir($control,$archivo){
        if(file_exists(PLANTILLAS.Registro::Leer("FPLANTILLA").DS.$this->tpl.'.tpl'))
            $this->plantilla = PLANTILLAS.Registro::Leer("FPLANTILLA").DS.$this->tpl.'.tpl';
        else
            $this->msg[]="La plantilla no existe";
        
        $pagina=PAG.strtolower($control).DS.$archivo.".html";
        $body='';
        
        extract($this->datos);
        
        $this->Conector();
        
        if(!file_exists($pagina))
            $this->msg[]="Debe crear el archivo: ".$pagina;
        else{
            ob_start();
            include($pagina);
            $body=ob_get_contents();
            ob_end_clean();
        }
        
        if((!isset($Ajax) || $Ajax!="activo") && file_exists($this->plantilla)){
            preg_match_all("/<script[^>]*>(.+?)<\/script>/si",$body,$script);
            $body=preg_replace("#<script[^>]*>(.+?)</script>#is"," ",$body);
            Registro::Escribir('script',join("\n",$script[1]),true);
            
            $ErrorMsg=$this->ErrorMsg();
            
            ob_start();
            include($this->plantilla);
            $HTML=ob_get_contents();
            ob_end_clean();
    
            return $HTML;
        }else
            return $body;
    }
    
    public function asigna_variables($vars){
        foreach($vars as $cve => $valor){
            $this->datos[$cve]=$valor;
        }
    }
    
    public function tpl($archivo){
        $this->tpl=$archivo;
    }
}
