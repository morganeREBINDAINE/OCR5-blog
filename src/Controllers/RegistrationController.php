<?php

namespace OCR5\Controllers;

use OCR5\Exceptions\AlreadyRegisteredException;
use OCR5\Services\Manager;
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
            // test if password are corrects
            // test if values are corrects
            $registrationManager = new RegistrationManager();
            //test if exists in db
            //test add in db
            $this->addFlash('success', 'ouiiiiiii');
        }

        return $this->render('authentication/registration');
    }
}
