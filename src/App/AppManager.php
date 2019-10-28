<?php

namespace OCR5\App;

use OCR5\Database\DatabaseMySQL;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigTest;

class AppManager
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
            $twig->addFunction(new TwigFunction('css', function ($value) {
                return 'style/' . $value . '.css';
            }));
            $twig->addFunction(new TwigFunction('img', function ($value) {
                return 'img/' . $value;
            }));
            $twig->addGlobal('session', $_SESSION);

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
        echo(AppManager::getTwig())->render('errors/404.html.twig');
    }
}
