<?php
class Extension extends Plugins{
	
    public function dateFilter($timestamp, $format = 'F j, Y H:i'){
        return date($format,$timestamp);
    }
    
    public function NumberFormat($number){
		return number_format($number,2);
	}
}
?>
