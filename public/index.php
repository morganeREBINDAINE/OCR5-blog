<?php

use OCR5\App\App;

require('../vendor/autoload.php');

session_start();

$router = new AltoRouter();

$router->map('GET', '/', 'OCR5\Controllers\BlogController::home');
$router->map('GET|POST', '/connexion', 'OCR5\Controllers\AuthenticationController::connection');
$router->map('POST', '/deconnexion', 'OCR5\Controllers\AuthenticationController::disconnection');
$router->map('GET|POST', '/inscription', 'OCR5\Controllers\RegistrationController::registration');
$router->map('GET|POST', '/profil', 'OCR5\Controllers\AdminController::profile');
$router->map('GET', '/gestion-redacteurs', 'OCR5\Controllers\AdminController::contributorsList');
$router->map('POST', '/demandes-redacteurs', 'OCR5\Controllers\AdminController::contributorsRequests');
$router->map('GET|POST', '/rediger-article', 'OCR5\Controllers\AdminController::writePost');

$match = $router->match();

App::init();

if (is_array($match) && is_callable($match['target'])) {
    list($controller, $method) = explode('::', $match['target']);
    call_user_func_array([new $controller(), $method], $match['params']);
} else {
    App::error404();
}
