<?php

namespace OCR5\Controllers;

use OCR5\App\AppManager;
use Twig\TwigFunction;

class BlogController extends Controller
{
    public function home()
    {
        $database = AppManager::getDatabase();
        return $this->render('blog/home');

    }


}
