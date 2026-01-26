<?php
class Contrarecibos extends Modelo{
	var $nombre="contrarecibos";
	var $unir=array("inner.proveedores"=>array("id"=>"proveedor"),"left.facturas"=>array("contrarecibo"=>"a.id"));
	
}
?>
