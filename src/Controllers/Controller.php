<?php

namespace OCR5\Controllers;

use OCR5\App\AppManager;
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
            echo(AppManager::getTwig())->render($templatePath, $vars);
        } catch (LoaderError $e) {
            echo(AppManager::getTwig())->render('errors/error.html.twig', [
                'message' => "Attention, développeuse ! Il y a un problème : le template que tu tentes de définir" . $templatePath . "n'existe pas !"
            ]);
        } catch (RuntimeError $e) {
            echo $e->getMessage();
        } catch (SyntaxError $e) {
            echo $e->getMessage();
        }
        unset($_SESSION['flashbag']);
    }

    protected function isConnected()
    {
        return isset($_SESSION['token']);
    }

    protected function addFlash($subject, $message)
    {
        $_SESSION['flashbag'][$subject] = $message;
    }
}
