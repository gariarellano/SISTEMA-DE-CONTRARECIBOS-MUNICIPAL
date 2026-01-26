<?php
/*
 * cargar.php
 * * Copyright 2016  ROMÁN GÓMEZ SOLACHE <solache@outlook.com>
 * */
define("DS",DIRECTORY_SEPARATOR);
define('SERVIDOR',$_SERVER['SERVER_NAME']);
define('URLCORTA',dirname($_SERVER['PHP_SELF']));
define("RUTA",dirname(dirname(__FILE__)).DS);
define("NUCLEO",RUTA."nucleo".DS);
define("CACHE",RUTA."cache".DS);
define("OBJ",NUCLEO."objetos".DS);
define("CTRL",RUTA."controles".DS);
define("MODELO",RUTA."modelos".DS);
define("PLANTILLAS",RUTA."plantillas".DS);
define("PAG",RUTA."paginas".DS);
define("PLUGINS",NUCLEO."plugins".DS);
define("LIB",NUCLEO."lib".DS);
define("TWIG",LIB."Twig".DS);
define("PLUG",RUTA."plugins".DS);
define("SCRIPT",RUTA."script".DS);
define("IDIOMA",RUTA."idiomas".DS);

// ¡NUEVO! Verifica si SID ya está definida antes de definirla.
// Esta es la línea 25 que causaba el aviso.
if (!defined('SID')) {
    define("SID",session_id());
}

/************* Calcular URL del sitio *****************************/
$ruta=explode("/",trim(URLCORTA,"/\\"));
$rur=str_replace("//","/",SERVIDOR."/".rtrim($ruta[0],'/')."/");
if(strstr(SERVIDOR,"https://")){
    define("URLSITIO",$rur);
}else{
    define("URLSITIO","https://".$rur);
}
$rs=str_replace("//","/",URLSITIO.isset($_GET['url'])?$_GET['url']:"");

unset($ruta);
define("URLCOMPLETA",$rs);
/************ Fin Calcular URL ************************************/

include(NUCLEO."autoload.php");

if(file_exists("configuracion.php"))
    include("configuracion.php");
else{
    $c=explode("/",isset($_GET['url'])?$_GET['url']:"");
    if(strtolower($c[0])=="instalar"){
        Registro::Escribir("funcion","index");
        Registro::Escribir("parametros","");
    }else
        header("Location: ".URLSITIO."Instalar/");
}

$MVC=new Despachador();
