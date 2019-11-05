<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\Services\AuthenticationManager;
use OCR5\Traits\FlashbagTrait;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class Controller
{
    use FlashbagTrait;

    protected function render($template, $vars = [])
    {
        $templatePath = $template . '.html.twig';
        try {
            echo(App::getTwig())->render($templatePath, $vars);
        } catch (LoaderError $e) {
            echo(App::getTwig())->render('errors/error.html.twig', [
                'message' => "Attention, développeuse ! Il y a un problème : le template que tu tentes de définir: \"" . $templatePath . "\" n'existe pas !"
            ]);
        } catch (RuntimeError $e) {
            echo $e->getMessage();
        } catch (SyntaxError $e) {
            echo $e->getMessage();
        }
    }

    protected function isConnected()
    {
        return (new AuthenticationManager())->compareTokens($_SESSION['user']);
    }

    protected function addFlash($subject, $message)
    {
        $_SESSION['flashbag'][$subject] = $message;
    }

    protected function error($message)
    {
        return $this->render('errors/error.html.twig', [
            'message' => $message
        ]);
    }
}
