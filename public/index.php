<?php

use OCR5\App\App;

require('../vendor/autoload.php');

session_start();

$router = new AltoRouter();

$router->map('GET', '/', 'OCR5\Controllers\BlogController::home');
$router->map('GET|POST', '/article-[i:id]', 'OCR5\Controllers\BlogController::showPost');
$router->map('GET', '/articles', 'OCR5\Controllers\BlogController::postsList');

$router->map('GET|POST', '/connexion', 'OCR5\Controllers\AuthenticationController::connection');
$router->map('POST', '/deconnexion', 'OCR5\Controllers\AuthenticationController::disconnection');
$router->map('GET|POST', '/inscription', 'OCR5\Controllers\AuthenticationController::registration');
$router->map('GET', '/profil', 'OCR5\Controllers\AdminController::profile');
$router->map('GET', '/gestion-[a:frenchEntity]', 'OCR5\Controllers\AdminController::handleEntities');
$router->map('GET|POST', '/rediger-article', 'OCR5\Controllers\AdminController::writePost');
$router->map('POST', '/action-entities', 'OCR5\Controllers\AdminController::actionEntities');
$router->map('GET', '/mes-articles', 'OCR5\Controllers\AdminController::myArticles');
$router->map('GET|POST', '/modifier-article-[i:id]', 'OCR5\Controllers\AdminController::writePost');

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    list($controller, $method) = explode('::', $match['target']);
    call_user_func_array([new $controller(), $method], $match['params']);
} else {
    App::error404();
}
unset($_SESSION['flashbag']);
$_SESSION['last_page'] = 'http://blog'.$_SERVER['REQUEST_URI'];