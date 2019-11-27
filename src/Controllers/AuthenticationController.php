<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\App\Post;
use OCR5\App\Session;
use OCR5\Handler\UserHandler;
use OCR5\Services\AuthenticationManager;
use OCR5\Services\EntityManager;
use OCR5\Services\FormManager;

class AuthenticationController extends Controller
{
    public function connection()
    {
        if ($this->isConnected()) {
            $this->redirect('/');
        }

        $error = null;

        if (App::isPostMethod()
            && null !== ($username= Post::get('username'))
            && null !== ($password= Post::get('password'))
        ) {
            $authenticationManager = new AuthenticationManager();
            if ($user = $authenticationManager->checkLogin($username, $password)) {
                $authenticationManager->startSession($user);

                return $this->redirect('/profil');
            }
            $this->addFlash('error', 'Erreur: pseudo et/ou mot de passe incorrects, ou compte non validé.');
        }

        return $this->render('authentication/connection');
    }

    public function disconnection()
    {
        Session::destroy();
        $this->redirect('/');
    }

    public function registration()
    {
        if ($this->isConnected()) {
            $this->redirect('/');
        }

        $message = null;

        if (App::isPostMethod()
            && null !== Post::get('username')
            && null !== Post::get('password')
            && null !== Post::get('passwordConfirm')
            && null !== Post::get('email')
            && false === (new FormManager())->checkRegistrationFormErrors(Post::get())
        ) {
            (new UserHandler())->create(Post::get()) ?

                    $this->addFlash('success', 'Votre candidature a été soumise.')
                    : $this->addFlash('error', 'Il y a eu un soucis durant la soumission de la candidature...');
        }
        return $this->render('authentication/registration');
    }
}
