<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\App\Session;
use OCR5\Services\AuthenticationManager;
use OCR5\Traits\FlashbagTrait;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class Controller
{
    use FlashbagTrait;

    protected function render($template, $vars = [])
    {
        $template = $template . '.html.twig';
        $app = new App();
        try {
            echo($app->getTwig())->render($template, $vars);
        } catch (LoaderError $e) {
            echo($app->getTwig())->render('errors/error.html.twig', [
                'message' => "Attention, développeuse ! Il y a un problème : le template que tu tentes de définir: \"" . $template . "\" n'existe pas !"
            ]);
        } catch (Error $e) {
            echo($app->getTwig())->render('errors/error.html.twig', [
                'message' => $e->getMessage()
            ]);
        }
    }

    protected function isConnected()
    {
        $authManager = new AuthenticationManager();
        return $authManager->compareTokens() && $authManager->ensureIdentity();
    }

    protected function error($message)
    {
        return $this->render('errors/error', [
            'message' => $message
        ]);
    }

    protected function redirect($path)
    {
        header('Location: http://blog' . $path);
        Session::unset('flashbag');
        exit();
    }
}
