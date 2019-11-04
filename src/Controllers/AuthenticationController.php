<?php

namespace OCR5\Controllers;

use OCR5\Services\AuthenticationManager;
use OCR5\Services\Manager;

class AuthenticationController extends Controller
{
    public function connection()
    {
        if ($this->isConnected()) {
            header('location: http://blog/');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
            $authenticationManager = new AuthenticationManager();
            if ($authenticationManager->checkLogin($_POST['username'], $_POST['password'])) {
                $authenticationManager = new AuthenticationManager();
                $authenticationManager->startSession($_POST['username']);

                header('Location: http://blog/profil');
                exit();
            }
            $this->addFlash('error', 'Erreur: pseudo et/ou mot de passe incorrects, ou compte non validé.');
        }

        return $this->render('authentication/connection');
    }

    public function disconnection()
    {
        session_destroy();
        header('location: http://blog/');
    }

}
