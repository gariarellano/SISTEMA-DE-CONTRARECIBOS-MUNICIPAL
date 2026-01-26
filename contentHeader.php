<?php
	$pi=pathinfo($_SERVER['PHP_SELF']);
	$ext=$pi['extension'];
	if($ext=='css')header("Content-type: text/css");
	elseif($ext=='js')header("Content-type: text/javascript");
	header('ExpiresDefault "access plus 31 days"');
?>