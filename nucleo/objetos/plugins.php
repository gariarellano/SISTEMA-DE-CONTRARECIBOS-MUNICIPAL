<?php
class Plugins {
	var $NameForm;
	var $Script;
	var $vista;
		
	var $tags=array(
		"form"=>"<form id='%s' action='%s' method='%s' %s>\n",
		"input"=>"<input type='%s' name='%s' %s />",
		"submit"=>"<input type='%s' %s />",
		"textarea"=>"<textarea name='%s' %s>%s</textarea>",
		"select"=>"<select name='%s' %s>%s</select>",
		"javascript"=>"<script src='%s.js' type='text/javascript'></script>"
	);
	
	
	function __construct(){		
		$this->NameForm=strtolower(substr(Registro::Leer('control'),0,-7));
		$this->vista=Vista::Instancia();
	}

	function __toString(){
		return get_class($this);
	}
}

?>
