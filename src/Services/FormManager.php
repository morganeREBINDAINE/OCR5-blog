<?php

namespace OCR5\Services;

class FormManager extends Manager
{
    public function checkPostFormErrors($formData)
    {
        $error = false;

        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $this->addFlash('error', 'Merci de remplir tous les champs.');
                $error = true;
                break;
            }
        }

        if (strlen($formData['title']) > 50 || strlen($formData['title']) < 3) {
            $this->addFlash('errorTitle', 'Le titre doit contenir entre 3 et 50 caractères.');
            $error = true;
        }

        if (strlen($formData['chapo']) > 200 || strlen($formData['chapo']) < 5) {
            $this->addFlash('errorChapo', 'La chapô doit contenir entre 5 et 200 caractères.');
            $error = true;
        }

        return $error;
    }

    public function checkRegistrationFormErrors($formData)
    {
        $error = false;

        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $this->addFlash('error', 'Merci de remplir tous les champs.');
                break;
            }
        }

        if ($result = $this->findStatusContributor($formData['username'], $formData['email'])) {
            // @todo const

            switch ($result['status']) {
                case '0':
                    $this->addFlash(
                        'error',
                        'Ce contributeur existe déjà.'
                    );
                    break;
                case 1:
                    $this->addFlash(
                        'error',
                        'Il y déjà une demande d\'inscription pour ce contributeur. Patience ! L\'administratrice va bientôt traiter votre cas.'
                    );
                    break;
            }
            return true;
        }
        if (strlen($formData['password']) < 5  || empty($formData['password'])) {
            $this->addFlash('errorPasswordEmpty', 'Vous devez entrer un mot de passe d\'au moins 5 caractères.');
            $error = true;
        }
        if ($formData['password'] !== $formData['passwordConfirm']) {
            $this->addFlash('errorPasswordConfirm', 'Les mots de passes ne sont pas les mêmes.');
            $error = true;
        }
        if (0 === preg_match('#^[a-zA-Z]{4,15}$#', $formData['username'])) {
            $this->addFlash('errorUsername', 'Le pseudo doit contenir 4 à 15 caractères (lettres uniquement).');
            $error = true;
        }
        if (false === filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('errorEmail', 'L\'email entré est incorrect.');
            $error = true;
        }

        return $error;
    }

    private function findStatusContributor($username, $email)
    {
        return $this->queryDatabase("SELECT status FROM user WHERE username = :username OR email = :email", [
            ':username' => $username,
            ':email' => $email
        ]);
    }
}