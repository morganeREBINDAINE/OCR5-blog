<?php

namespace OCR5\Controllers;

use Twig\TwigFunction;

class BlogController
{
    public function home()
    {
//        require ('../templates/test.html');die();
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, [
//            'cache' => '../cache'
        ]);
        $twig->addFunction(new TwigFunction('css', function($value) {
            return  'style/' .$value. '.css';
        }));
        $twig->addFunction(new TwigFunction('img', function($value) {
            return  'img/' .$value;
        }));

        echo $twig->render('blog/home.html.twig');
    }
}
