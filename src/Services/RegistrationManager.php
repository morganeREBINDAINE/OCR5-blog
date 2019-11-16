<?php

namespace OCR5\Services;

class RegistrationManager extends Manager
{
    public function checkRegistrationFormErrors($formData)
    {
        $error = false;

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

    public function createContributor($formData)
    {
        return $this->queryDatabase("INSERT INTO user (username, email, password, role, status, added) VALUES (:username, :email, :password, 'contributor', 0, NOW())", [
            ':username' => $formData['username'],
            ':password' => password_hash($formData['password'], PASSWORD_DEFAULT),
            ':email' => $formData['email']
        ]);
    }

    private function findStatusContributor($username, $email)
    {
        return $this->queryDatabase("SELECT status FROM user WHERE username = :username OR email = :email", [
            ':username' => $username,
            ':email' => $email
        ]);
    }
}
