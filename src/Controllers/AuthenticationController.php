<?php

namespace OCR5\Controllers;

use OCR5\Services\AuthenticationManager;
use OCR5\Services\EntityManager;
use OCR5\Services\FormManager;

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
            if ($user = $authenticationManager->checkLogin($_POST['username'], $_POST['password'])) {
                $authenticationManager->startSession($user);

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

    public function registration()
    {
        if ($this->isConnected()) {
            header('location: http://blog/');
        }

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['username'], $_POST['password'], $_POST['passwordConfirm'], $_POST['email'])
            && false === (new FormManager())->checkRegistrationFormErrors($_POST)
        ) {
            (new EntityManager())->createContributor($_POST) ?
                    $this->addFlash('success', 'Votre candidature a été soumise.')
                    : $this->addFlash('error', 'Il y a eu un soucis durant la soumission de la candidature...');
        }
        return $this->render('authentication/registration');
    }
}
