<?php
/*
 *  Form.php
 *  Plugin para los formularios
 *  Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 *  	
 */
class Form extends Plugins{
	var $subAct=false;
	var $legAct=false;
	
	function crear($action,$name='',$method='post', $otro=''){
		$this->subAct=false;
		$this->legAct=false;
		
		if($name=='')
			$name=$this->NameForm;
		
		if(is_array($action)){
			$action=$action['control']."/".(isset($action['funcion'])?$action['funcion']:"index")."/";
		}else{
			$action=substr(Registro::Leer('control'),0,-7)."/".$action;	
		}
		
		if(Registro::Leer("LANG"))
			$action=URL.$action;
		
		return sprintf($this->tags["form"], $name, $action,$method,$otro);
	}
	
	function input($nombre,$option=array()){
		$formato = "<%s %s='%s'>%s</%s>";
		$Return="";
		$m=array();
		$type=isset($option['type'])?$option['type']:"text";
		$name=isset($option['name'])?$option['name']:$this->NameForm."_".$nombre;
		$previo=isset($option['previo'])?$option['previo']:null;
		
		if(isset($option['label'])){
			$id=isset($option['id'])?$option['id']:$name;
			$option['id']=$id;
			
			$etiqueta = (is_array($option['label']) && isset($option['label'][1]))?$option['label'][1]:"label";
			$attributo=(is_array($option['label']) && isset($option['label'][2]))?"class":"for";
			$valor=(is_array($option['label']) && isset($option['label'][2]))?$option['label'][2]:$id;
			$cadena=(is_array($option['label']) && isset($option['label'][0]))?$option['label'][0]:$option['label'];
			
			$Return = sprintf($formato,$etiqueta, $attributo, $valor, $cadena, $etiqueta);
		}
		
		unset($option['name'],$option['type'],$option['label'],$option['previo']);
		
		foreach($option as $cve => $valor)
			$m[]=$cve."='".$valor."'";
		
		
		$input = sprintf($this->tags["input"],$type,$name,join(" ",$m));
		
		if($previo != null){
			$input = sprintf($formato, $previo[0], "class", $previo[1], $input, $previo[0]);
		}
		
		return $Return . $input;
	}
	
	function textarea($nombre,$option=array()){
		$Return="";
		$m=array();
		$name=isset($option['name'])?$option['name']:$this->NameForm."_".$nombre;
		$value=isset($option['value'])?$option['value']:"";
		
		if(isset($option['label'])){
			$id=isset($option['id'])?$option['id']:$name;
			$option['id']=$id;
			$Return.="<label for='".$id."'>".$option['label']."</label>";
		}
		
		unset($option['name'],$option['value'],$option['label']);
		
		foreach($option as $cve => $valor)
			$m[]=$cve."='".$valor."'";	
		
		return $Return . sprintf($this->tags["textarea"],$name,join(" ",$m),$value);
	}
	
	function select($nombre,$optiones=array(), $option=array()){
		$Return="";
		$m=array();
		$op=array();
		$name=isset($option['name'])?$option['name']:$this->NameForm."_".$nombre;
		$option['id']=isset($option['id'])?$option['id']:$name;
		
		unset($option['name']);
		
		foreach($option as $cve => $valor)
			$m[]=$cve."='".$valor."'";
			
		foreach($optiones as $cve => $va)
				$op[]="<option value='".$cve."'>".$va."</option>";
		
		
		if(isset($option['label']))
			$Return.="<label for='".$option['id']."'>".$option['label']."</label>";
		
		return $Return . sprintf($this->tags["select"],$name,join(" ",$m),join(" ",$op));
	}
	
	function legenda($texto=false){
		$this->legAct=true;
		return "<fieldset>".($texto?"<legend>".$texto."</legend>":"");	
	}
	
	function submit($nombre,$src="",$option=array()){
		$this->subAct=true;
		$m=array();
		$type=isset($option['type'])?$option['type']:"submit";
					
		if(!isset($option['value']))
			$option['value']=$nombre;
		
		if($src!=""){
			$option['src']=$src;
			$type="image";
		}
		
		unset($option['type']);
		
		foreach($option as $cve => $valor)
			$m[]=$cve."='".$valor."'";
		
		return sprintf($this->tags["submit"],$type,join(" ",$m));
	}
	
	function fin($nombre="",$src="",$option=array()){
		if($nombre!="")
			return (!$this->subAct?$this->submit($nombre,$src,$option):"") . ($this->legAct?"</fieldset>":"") . "</form>"; 
		else
			return ($this->legAct?"</fieldset>":"") . "</form>"; 
	}

}

?>
