<?php
require 'config.php';
require 'autoload.php';
echo "PDO: ";
var_dump($pdo);
echo "<br>";
echo "Clase Usuario: ";
var_dump(class_exists('Usuario'));
echo "<br>";
echo "Clase AdminController: ";
var_dump(class_exists('AdminController'));
