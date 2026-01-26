<?php
/*
 * basedatos.php
 * * Copyright 2016 ROMÁN GÓMEZ SOLACHE<solache@outlook.com>
 * */

class Basedatos{
    private $conexion;
    private $log;
    var $error;
    static $instancia=null;
    
    function __construct(){
        $this->log=LogDb::Instancia();
        if(Registro::Verificar("BD_HOST")){
            try{
                $this->conexion = new PDO("mysql:host=".Registro::Leer("BD_HOST").";dbname=".Registro::Leer("BD_NAME"),Registro::Leer("BD_USER"),base64_decode(Registro::Leer("BD_PASS")),array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            }catch(PDOException $e){
                Registro::Escribir("MSG","Ha Ocurrido un error al conectarse",true);
            }
        }
        self::$instancia=$this;
    }

    public function Consultar($tabla,$campos='*',$parametros=null,$unir=false,$indice=true){
        
        $query="select {$campos} from {$tabla}";
        $group='';
        $opcion=array();
        
        if(is_array($parametros)){
            
            
            if(isset($parametros['group'])){
                $group.=' group by '.$parametros['group'];
                unset($parametros['group']);
            }
            if(isset($parametros['order'])){
                $group.=' order by '.$parametros['order'];
                unset($parametros['order']);
            }
            if(isset($parametros['like'])){ 
                $opcion[]=$this->generarLike($parametros['like'],"like");
                unset($parametros['like']);
            }
            
            if(isset($parametros['notlike'])){
                $opcion[]=$this->generarLike($parametros['notlike'],"not like");
                unset($parametros['notlike']);
            }
            //if(isset($parametros))print_r($parametros);echo "<br>";
            $a=1;foreach($parametros as $cve => $valor){
                                //echo $cve."<br>";
                if(is_array($valor))
                    foreach($valor as $v => $c){
                        if(!is_array($parametros[$cve]))
                            {
                            if($cve=="string")
                                $opcion[]=$v."='".$c."'";
                            else
                                $opcion[]=$v."=".$c."";
                            }
                            else
                            {//echo "here";
                                        foreach($parametros[$cve] as $d => $e){if($a==1){
                                $opcion[]=$cve.">='".$e."'";    $a++;}else    {if($a==2)         
                                $opcion[]=$cve."<='".$e."'";$a++;}
                                }unset($parametros[key($valor)]);
                            }
                    }
                else
                    $opcion[]=$cve."='".$valor."'";
                    //print_r($parametros);
            }
            
        }else if($parametros!=null)
            $opcion[]=$parametros;
        
        if($unir)
            $query.=$this->generarJOIN($tabla);
        
        if(count($opcion)>0)
            // CAMBIO: Invertir el orden de los argumentos para PHP 8+
            $query.=" where ".join(" and ", $opcion);
    
        $query.=$group;
        //echo $query; 
        
        return $this->EjecutarSql($query,false,$indice);
    }
    
    public function EjecutarSql($query,$recuperar=false,$indice=true){
        
        try{
            $smt=$this->conexion->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $smt->execute();
            
            $datos=$smt->fetchAll(PDO::FETCH_ASSOC);
            $this->error=$smt->errorInfo();
            $smt=null;
            $this->log->agregar(array("query"=>$query,"error"=>$this->error,"count"=>count($datos)));
            
            
            if($recuperar){
                $last=$this->EjecutarSql("SELECT LAST_INSERT_ID() as lastId");
                return $last[0]['lastId']==0?1:$last[0]['lastId'];
            }else{
                if(count($datos)==1 && !$indice)
                    return $datos[0];
                else 
                    return $datos;
            }
        }catch (PDOException $e) {
          Registro::Escribir("MSG","Ha ocurrido un error al obtener los datos");
       }
    }
    
    public function Insertar($tabla,$datos=array()){
        $campos=array();
        $valores=array();
        
        foreach($datos as $cve => $valor){
            $campos[]="`".$cve."`";
            $valores[]="'".$valor."'";
        }
        
        // CAMBIO: Invertir el orden de los argumentos para PHP 8+
        $query="insert into {$tabla} (".join(",",$campos).") values(".join(",",$valores).")";
    //echo $query; die();
        
        return $this->EjecutarSql($query,true);
    }
    
    public function Actualizar($tabla,$datos=array(), $parametros=array()){
        $campos=$opcion=$valores=array();
        
        foreach($datos as $cve => $valor){
            $campos[]=$cve;
            $valores[]="'".$valor."'";
        }
        
        $query="UPDATE ".$tabla." SET ";
        for ($i=0;$i<count($campos);$i++){
            if ($i==(count($campos)-1)){
                $query.="`".$campos[$i]."`=".$valores[$i];
            }else{
                $query.="`".$campos[$i]."`=".$valores[$i].", ";
            }
        }
        
        foreach($parametros as $cve => $valor){
            if(is_array($valor))
                foreach($valor as $v => $c){
                    if($cve=="string")
                        $opcion[]=$v."='".$c."'";
                    else
                        $opcion[]=$v."=".$c."";
                }
            else
                $opcion[]=$cve."='".$valor."'";
        }
            
        // CAMBIO: Invertir el orden de los argumentos para PHP 8+
        $query.=" where ".join(" and ", $opcion);
        
        return $this->EjecutarSql($query,false);
    }
    
    public function Eliminar($tabla,$parametros=array()){
        $opcion=array();
        
        foreach($parametros as $cve => $valor){
            if(is_array($valor))
                foreach($valor as $v => $c){
                    if($cve=="string")
                        $opcion[]=$v."='".$c."'";
                    else
                        $opcion[]=$v."=".$c."";
                }
            else
                $opcion[]=$cve."='".$valor."'";
        }
        
        // CAMBIO: Invertir el orden de los argumentos para PHP 8+
        $query="delete from {$tabla} where ".join(" and ", $opcion);
        return $this->EjecutarSql($query,false);
    }

    private function generarJOIN($tabla){
        $join=" a ";
        $chr=97;
        if(isset($this->unir_a)){
            if(!is_array($this->unir_a))
                $join.="inner join {$this->unir_a} b  on b.id_{$tabla_a}=a.id";
            else{
                foreach($this->unir_a as $cve => $valor){
                    $chr++;
                    $char=chr($chr);
                    if(!is_array($valor))
                            $join.="inner join {$cve} {$char} on {$char}.id_{$valor}=a.id ";
                    else{
                        $tabla=$cve;
                        $and=array();
                        foreach($valor as $key =>$value){
                            if(is_array($value)){
                                $tabla=$key;
                                foreach($value as $c => $v){
                                    $and[]=" {$char}.{$c}=".(!strstr($v,".")?"a.":"").$v;
                                }
                            }else{
                                $and[]=" {$char}.{$key}=".(!strstr($value,".")?"a.":"").$value;
                            }
                        }
                           
                        if(strstr($tabla,".")){
                            $aux=explode(".",$tabla);
                            $tabla=$aux[1];
                        }
                        
                        // Esta llamada a join ya estaba en el orden correcto (string, array)
                        $join.=" ".(isset($aux)?$aux[0]:"inner")." join  {$tabla} {$char} on ".join(" and ",$and);
                        
                        unset($aux);
                    }
                }
            }
        }else if(isset($this->unir)){
            if(!is_array($this->unir))
                $join.="inner join {$this->unir} b  on b.id_{$tabla}=a.id";
            else{
                foreach($this->unir as $cve => $valor){
                    $chr++;
                    $char=chr($chr);
                    if(!is_array($valor))
                            $join.="inner join {$cve} {$char} on {$char}.id_{$valor}=a.id ";
                    else{
                        $tabla=$cve;
                        $and=array();
                        foreach($valor as $key =>$value){
                            if(is_array($value)){
                                $tabla=$key;
                                foreach($value as $c => $v){
                                    $and[]=" {$char}.{$c}=".(!strstr($v,".")?"a.":"").$v;
                                }
                            }else{
                                $and[]=" {$char}.{$key}=".(!strstr($value,".")?"a.":"").$value;
                            }
                        }
                           
                        if(strstr($tabla,".")){
                            $aux=explode(".",$tabla);
                            $tabla=$aux[1];
                        }
                           
                        // Esta llamada a join ya estaba en el orden correcto (string, array)
                        $join.=" ".(isset($aux)?$aux[0]:"inner")." join  {$tabla} {$char} on ".join(" and ",$and);
                    }
                }
            }
        }
        
        return $join;
    }
    
    private function generarLike(&$array,$tipo){ //print_r($array);echo "<br>";
        $opcion=array();
        foreach($array as $clave => $valor){
            $string=" %s '%s%s%s'";
            //print_r( $valor);
            $tmp=array("","");
            if(isset($valor[1])){
                 if(strtoupper($valor[1])=="L" || strtoupper($valor[1])=="B")
                     $tmp[0]="%";
                 if(strtoupper($valor[1])=="R" || strtoupper($valor[1])=="B")
                     $tmp[1]="%";
            }
            //echo $tipo.$tmp[0].$valor[0].$tmp[1]."<br>";
            $opcion[]=$clave.(sprintf($string,$tipo,$tmp[0],$valor[0],$tmp[1]));
        }
        // CAMBIO: Invertir el orden de los argumentos para PHP 8+ (Línea 85 original)
        return join(" and ", $opcion);
    }
    
    public function MostrarLog(){
        return $this->log->mostrar();   
    }
}
