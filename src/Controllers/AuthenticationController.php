<?php

namespace OCR5\Controllers;

use OCR5\Services\AuthenticationManager;
use OCR5\Services\DatabaseManager;

class AuthenticationController extends Controller
{
    public function connexion()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
            $databaseManager = new DatabaseManager();
            if ($databaseManager->checkLogin($_POST['username'], $_POST['password'])) {
                $authenticationManager = new AuthenticationManager();
                $authenticationManager->registerSession($_POST['username']);

                header('Location: http://blog/profil');
                exit();
            }
            $error = 'Erreur: pseudo et/ou mot de passe incorrects';
        }

        return $this->render('authentication/connection', ['error' => $error]);
    }
}
