<?php


require('../vendor/autoload.php');


//$controller = new OCR5\Controllers\BlogController();
//call_user_func_array([$controller, 'home'], ['params envoyés']);
//die();
$router = new AltoRouter();

$router->map('GET', '/', 'OCR5\Controllers\BlogController::home');
//$router->map('GET', '/contact', 'controller:action');

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    list($controller, $method) = explode('::', $match['target']);
    call_user_func_array([new $controller(), $method], $match['params']);
}
