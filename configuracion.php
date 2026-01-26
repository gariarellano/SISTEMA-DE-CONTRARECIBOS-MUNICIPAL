<?php
/*
 *      configuracion.php
 *      
 *    
 * 
 */

/***** Datos para la cache ****/
Registro::Escribir("CACHEACTIVO","no");
Registro::Escribir("CACHETIEMPO",30);

/***** Datos para la conexión a la BD ****/
Registro::Escribir("BD_HOST","localhost");
Registro::Escribir("BD_NAME","huetamoc_facturas");
Registro::Escribir("BD_USER","huetamoc_root20");
Registro::Escribir("BD_PASS",base64_encode("obrashuetamo2015")); /* bm9ybWFu  La contraseña debe de escribirse encriptada con base64*/
/*Contraseña invermobilia: T$D3LG4wO1t0
 */

/***** Control y funciones estables como default **************/
Registro::Escribir("control","indexControl");
Registro::Escribir("funcion","index");
Registro::Escribir("parametros","");

/*****  Datos para la carga por default *****/

Registro::Ruta("proveedores/",array("control"=>"proveedores","function"=>"index"));
Registro::Ruta("proveedores/agregar",array("control"=>"proveedores","function"=>"agregar"));
Registro::Ruta("proveedores/buscar",array("control"=>"proveedores","function"=>"index"));
Registro::Ruta("proveedores/borrar",array("control"=>"proveedores","funcion"=>"borrar"));

Registro::Ruta("contrarecibos/",array("control"=>"contrarecibos","function"=>"index"));
Registro::Ruta("contrarecibos/agregar",array("control"=>"contrarecibos","function"=>"agregar"));
Registro::Ruta("contrarecibos/buscar",array("control"=>"contrarecibos","function"=>"buscar"));
Registro::Ruta("contrarecibos/borrar",array("control"=>"contrarecibos","funcion"=>"borrar"));
Registro::Ruta("contrarecibos/buscarfacturas",array("control"=>"contrarecibos","funcion"=>"buscarfacturas"));

Registro::Ruta("facturas/",array("control"=>"facturas","function"=>"index"));
Registro::Ruta("facturas/agregar",array("control"=>"facturas","function"=>"agregar"));
Registro::Ruta("facturas/buscar",array("control"=>"facturas","function"=>"index"));
Registro::Ruta("facturas/pagar",array("control"=>"facturas","function"=>"pagar"));

Registro::Ruta("facturas/pagado",array("control"=>"facturas","function"=>"pagado"));
Registro::Ruta("facturas/borrar",array("control"=>"facturas","funcion"=>"borrar"));

Registro::Escribir("Admin","admin");


/*****  Selección de plantillas default *****/
Registro::Escribir("FPLANTILLA","facturas");


/*****  Selección de Idiomas *****/
Registro::Escribir("LANG",false);
Registro::Escribir("LANGDEFAULT","esp");

/*****  Metadatos para SEO *****/
Registro::Escribir("SEO",false);
Registro::Escribir("SEO_KEYWORD","");
Registro::Escribir("SEO_DESCRIPTION","");


/*****  Asignar Paginas enconstruccion *****/

/**** Asignar Ruta de las imagenes ********/
Registro::Escribir("IMG",array("images","servis","imgpropiedad","imgblog","banners","imgcategorias","imgdesarrollos","imgmodelos"));
Registro::Escribir("SESSION","ADMIN");
?>
