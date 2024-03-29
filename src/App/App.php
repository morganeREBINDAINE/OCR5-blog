<?php

namespace OCR5\App;

use AltoRouter;
use OCR5\Services\AuthenticationManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigTest;

class App
{
    private static $twig;

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        if (empty(self::$twig)) {
            $loader = new FilesystemLoader('../templates');
            $twig   = new Environment($loader, [
                'debug' =>true
//                'cache' => '../cache'
            ]);
            $twig->addExtension(new DebugExtension());
            $twig->addTest(new TwigTest('tokenValid', function () {
                return (new AuthenticationManager())->compareTokens();
            }));
            $twig->addFunction(new TwigFunction('css', function ($value) {
                return 'style/' . $value . '.css';
            }));
            $twig->addFunction(new TwigFunction('img', function ($value) {
                return 'img/' . $value;
            }));
            $twig->addFunction(new TwigFunction('hash', function ($value) {
                return base64_encode($value.'         ').'-'. password_hash((string)$value, PASSWORD_DEFAULT);
            }));
            $twig->addGlobal('session', Session::get());
            $twig->addGlobal('post', Post::get());

            self::$twig = $twig;
        }

        return self::$twig;
    }

    public function error404()
    {
        echo($this->getTwig())->render('errors/404.html.twig');
    }

    public function init()
    {
        Session::start();
        Post::start();

        $router = new AltoRouter();

        $router->map('GET|POST', '/', 'OCR5\Controllers\BlogController::home');
        $router->map('GET|POST', '/article-[i:id]', 'OCR5\Controllers\BlogController::showPost');
        $router->map('GET', '/articles', 'OCR5\Controllers\BlogController::postsList');

        $router->map('GET|POST', '/connexion', 'OCR5\Controllers\AuthenticationController::connection');
        $router->map('POST', '/deconnexion', 'OCR5\Controllers\AuthenticationController::disconnection');
        $router->map('GET|POST', '/inscription', 'OCR5\Controllers\AuthenticationController::registration');
        $router->map('GET', '/profil', 'OCR5\Controllers\AdminController::profile');
        $router->map('GET', '/gestion-[a:frenchEntity]', 'OCR5\Controllers\AdminController::handleEntities');
        $router->map('GET|POST', '/rediger-article', 'OCR5\Controllers\AdminController::writePost');
        $router->map('GET|POST', '/modifier-article-[i:identifier]', 'OCR5\Controllers\AdminController::writePost');
        $router->map('POST', '/action-entities', 'OCR5\Controllers\AdminController::actionEntities');
        $router->map('GET', '/mes-articles', 'OCR5\Controllers\AdminController::myArticles');

        $match = $router->match();

        if (is_array($match) && is_callable($match['target'])) {
            list($controller, $method) = explode('::', $match['target']);
            call_user_func_array([new $controller(), $method], $match['params']);
        } else {
            $this->error404();
        }
    }

    public static function isPostMethod()
    {
        return self::getServer('REQUEST_METHOD') === 'POST';
    }

    public static function getServer($key = null) {
        return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
    }
}
