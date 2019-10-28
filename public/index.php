<?php

use OCR5\App\AppManager;

require('../vendor/autoload.php');

session_start();

$router = new AltoRouter();

$router->map('GET', '/', 'OCR5\Controllers\BlogController::home');
$router->map('GET|POST', '/connexion', 'OCR5\Controllers\AuthenticationController::connection');
$router->map('POST', '/deconnexion', 'OCR5\Controllers\AuthenticationController::disconnection');
$router->map('GET', '/profil', 'OCR5\Controllers\BackController::profile');

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    list($controller, $method) = explode('::', $match['target']);
    call_user_func_array([new $controller(), $method], $match['params']);
}
else {
    AppManager::error404();
}
