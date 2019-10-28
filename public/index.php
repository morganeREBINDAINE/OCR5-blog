<?php

require('../vendor/autoload.php');

$router = new AltoRouter();

$router->map('GET', '/', 'OCR5\Controllers\BlogController::home');
//$router->map('GET', '/contact', 'controller:action');

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    list($controller, $method) = explode('::', $match['target']);
    call_user_func_array([new $controller(), $method], $match['params']);
}
