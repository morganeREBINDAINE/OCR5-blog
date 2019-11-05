<?php

namespace OCR5\Controllers;

use OCR5\Exceptions\AlreadyRegisteredException;
use OCR5\Services\RegistrationManager;

class RegistrationController extends Controller
{
    public function registration()
    {
        if ($this->isConnected()) {
            header('location: http://blog/');
        }

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['username'], $_POST['password'], $_POST['passwordConfirm'], $_POST['email'])) {
            $registrationManager = new RegistrationManager();
            if (false === $registrationManager->checkRegistrationFormErrors($_POST)) {
                $registrationManager->createContributor($_POST) ?
                    $this->addFlash('success', 'Votre candidature a été soumise.')
                    : $this->addFlash('error', 'Il y a eu un soucis durant la soumission de la candidature...');
            }

        }
        return $this->render('authentication/registration');
    }
}
