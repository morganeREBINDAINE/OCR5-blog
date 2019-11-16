<?php

namespace OCR5\Controllers;

use OCR5\App\AppManager;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class Controller
{
    protected function render($template, $vars = [])
    {
        $templatePath = $template . '.html.twig';
        try {
            echo(AppManager::getTwig())->render($templatePath, $vars);
        } catch (LoaderError $e) {
            echo(AppManager::getTwig())->render('errors/templateNotFound.html.twig', [
                'template' => $templatePath
            ]);
        } catch (RuntimeError $e) {
            echo $e->getMessage();
        } catch (SyntaxError $e) {
            echo $e->getMessage();
        }
    }
}
