<?php
spl_autoload_register(function($class){
    $controllerFile = __DIR__ . "/controllers/$class.php";
    $modelFile = __DIR__ . "/models/$class.php";

    if(file_exists($controllerFile)) require $controllerFile;
    elseif(file_exists($modelFile)) require $modelFile;
});
