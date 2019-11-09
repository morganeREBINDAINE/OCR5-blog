<?php

namespace OCR5\App;

use OCR5\Database\DatabaseMySQL;
use OCR5\Services\AuthenticationManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigTest;

class App
{
    private static $twig;
    private static $database;

    /**
     * @return Environment
     */
    public static function getTwig(): Environment
    {
        if (empty(self::$twig)) {
            $loader = new FilesystemLoader('../templates');
            $twig   = new Environment($loader, [
                //            'cache' => '../cache'
            ]);
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
            $twig->addGlobal('session', $_SESSION);
            $twig->addGlobal('post', $_POST);

            self::$twig = $twig;
        }

        return self::$twig;
    }

    public static function getDatabase()
    {
        if (empty(self::$database)) {
            $database = new DatabaseMySQL();
            self::$database = $database;
        }
        return self::$database;
    }

    public static function error404()
    {
        header("HTTP/1.0 404 Not Found");
        echo(App::getTwig())->render('errors/404.html.twig');
    }
}
