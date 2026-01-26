<?php
class LogDb{
	var $debug=array();	
	private static $instancia=null;
	
	private function __construct(){}
	
    public static function Instancia(){
        if (self::$instancia==null) {
            $c = __CLASS__;
            self::$instancia = new $c;
        }

        return self::$instancia;
    }
    
	function agregar($datos=array()){
		array_push($this->debug,$datos);
	}
	
	function obtener(){
		return $this->debug;
	}
	
	function mostrar(){
		$incre=0;
		$log="<table class='sql-log'>
			<caption>".count($this->debug)." Querys</caption>
			
			<tr>
				<th>Nr</th>
				<th>Sql</th>
				<th>Error</th>
				<th>Rows</th>
			</tr>";
			
		 foreach($this->debug as $key){
			$incre++;
			$log.= "
				<tr>
					<td>".$incre."</td>
					<td>".$key['query']."</td>
					<td>".(isset($key['error'][2])?$key['error'][2]:"")."</td>
					<td>".$key['count']."</td>
				</tr>";
		}
		$log.= "</table>";
		
		return $log;
	}
}
?>
