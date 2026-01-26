<?php
function cargar($className) {
	$possibilities = array(
		CTRL.str_replace("control","Control",strtolower($className)).'.php',
		OBJ.strtolower($className).'.php',
		MODELO.strtolower($className).'.php',
		NUCLEO.strtolower($className).'.php',
		PLUG.strtolower($className).'.php',
		PLUGINS.strtolower($className).'.php'
	);
		
	foreach ($possibilities as $file) {
		if (file_exists($file)) {		
			include_once($file);
			return true;
		}
	}
	
	return false;
}

spl_autoload_register('cargar');
?>
