<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\Services\UserManager;
use Twig\TwigFunction;

class BlogController extends Controller
{
    public function home()
    {
        return $this->render('blog/home');
    }


}
