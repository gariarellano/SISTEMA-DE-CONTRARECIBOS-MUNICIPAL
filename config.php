<?php

define('BASE_URL', 'https://facturasv2.huetamo.com.mx/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'huetamoc_contrarecibos');
define('DB_USER', 'huetamoc_contrarecibos');
define('DB_PASS', 'aUiMns_CWz;[CIH=');
define('DB_PORT', 3306);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}





