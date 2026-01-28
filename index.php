<?php
date_default_timezone_set('America/Mexico_City');
session_start();

header("Content-Type: text/html; charset=UTF-8");
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar configuración y autoload
require __DIR__ . '/config.php';
require __DIR__ . '/autoload.php';

// Determinar controlador y acción
$controller = isset($_GET['c']) ? ucfirst($_GET['c']) . 'Controller' : 'AdminController';
$action = isset($_GET['a']) ? $_GET['a'] : 'login';

// Verificar existencia del archivo del controlador
$controllerFile = __DIR__ . "/controllers/$controller.php";
if(!file_exists($controllerFile)) die("Controlador no encontrado: $controller");

// Instanciar el controlador
require $controllerFile;
$ctrl = new $controller($pdo);

// Verificar existencia del método
if(!method_exists($ctrl, $action)) die("Acción no encontrada: $action");

// Ejecutar la acción
$ctrl->$action();
