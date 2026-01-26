<?php
class Php extends Plugins{
	function BorrarArchivos($ruta){
		if(file_exists($ruta)){
			chmod($ruta,0777);
			if(is_dir($ruta)) {
				$handle = opendir($ruta);
				while($filename = readdir($handle)) {
					if ($filename != "." && $filename != "..") {
						$this->BorrarArchivos($ruta."/".$filename);
					}
				}
				closedir($handle);
				rmdir($ruta);
			}else unlink($ruta); 
		}
	}
	
	private function thumbnail_calcsize($w, $h, $square){
		$k = $square / max($w, $h);
		return array($w*$k, $h*$k);
	} 
	
	function thumbnail_generator($srcfile, &$params){
		// getting source image size
		@list($w, $h) = getimagesize($srcfile);
		if ($w == false)
			return false;

		// checking params array 
		if (!(is_array($params)&&is_array($params[0])))
			return false;
		
		$nombre=explode("/",strtolower($srcfile));
		$ext = explode(".",$nombre[count($nombre) - 1]);
		
		$im2=null;
		
		if ($ext[1] == "gif")
			$im2 = imagecreatefromgif($srcfile) or die("Error al leer  $srcfile!"); 
		else if($ext[1] == "jpg" || $ext[1] == "jpeg")
			$im2 = imagecreatefromjpeg($srcfile) or die("Error al leer $srcfile!");
		else if($ext[1] == "png") {
			$im2 = imagecreatefrompng($srcfile) or die("Error al leer $srcfile!");
			imagealphablending($im2, true);
		}else echo "sin soporte para $srcfile";
			
		for($i=0; $i<sizeof($params); $i++){
			$cur_w = $params[$i]['size'][0];
			$cur_h = $params[$i]['size'][1];
			$img_cur = imagecreatetruecolor($cur_w, $cur_h);
			
			if($ext[1] == "png") imagealphablending($img_cur, true);
			
			imagecopyresampled($img_cur, $im2, 0, 0, 0, 0, $cur_w, $cur_h, $w, $h);
			imageinterlace($img_cur, true);
			
			if ($ext[1] == "gif")
				imagegif($img_cur, $params[$i]['file'], 100);
			else if($ext[1] == "jpg" || $ext[1] == "jpeg")
				imagejpeg($img_cur, $params[$i]['file'], 100);
			else if($ext[1] == "png"){
				imagesavealpha($img_cur, true);
				imagepng($img_cur, $params[$i]['file'], 100);
			}
			imagedestroy($img_cur);
		}

		return true;
	}
	
	function diferencia_tiempo($fecha){
		$f1 = strtotime(date("Y-m-d H:i:s"));
		$f2 = strtotime($fecha);
		
		$seg = $f1 - $f2;
		$min = intval($seg/60);
		$seg = $seg % 60;
		$hor = intval($min / 60);
		$min = $min % 60;
		$dia = intval($hor / 24);
		$hor = $hor % 24;
		$mes = intval($dia / 30);
		$dia = $dia % 30;
		
		$return = "";
		if($mes>0) $return = $mes>1?$mes." meses":"un mes";
		else if($dia>0) $return = $dia>1?$dia." días":"un día";
		else if($hor>0) $return = $hor." hrs";
		else if($min>0) $return = $min." min";
		//$return = ($hor>0?$hor." hrs. ":"").($min>0?$min." min. ":"");
		return $return?$return:"un momento";
	}
}
?>
