<?php

namespace OCR5\Services;

use Verot\Upload\Upload;

class FormManager extends Manager
{
    public function checkImageErrors($image)
    {
        if (null === $image['file']) {
            $this->addFlash('errorImage', 'Vous devez ajouter une image à l\'article.');
            return true;
        }
        $handle = new Upload($image['file']);

        if ($handle->uploaded) {
            $handle->allowed = ['image/*'];
            $handle->jpeg_quality=100;
            if ($handle->image_src_y > 2000 || $handle->image_src_x > 2500) {
                $this->addFlash('errorImage', 'L\'image est incorrecte : Largeur maximale de 2500px, hauteur max 2000px.');
                return true;
            }

            if ($handle->image_src_x < 500) {
                $this->addFlash('errorImage', 'L\'image est trop petite : elle doit faire au moins 500px de largeur.');
                return true;
            }

            $handle->file_new_name_body   = $image['name'];
            $handle->process('img/');
            $handle->image_resize         = true;
            $handle->image_y              = 500;
            $handle->image_ratio_x        = true;
            $handle->file_new_name_body   = $image['name'] . '-mini';
            $handle->process('img/');
            if (false === $handle->processed) {
                $this->addFlash('errorImage', 'Il y a eu une erreur lors de l\'enregistrement de l\'image.');
                return true;
            }
        }
        return false;
    }

    public function checkPostFormErrors($formData, $image)
    {
        $error = false;

        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $this->addFlash('error', 'Merci de remplir tous les champs.');
                return true;
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

        if (strlen($formData['content']) < 100) {
            $this->addFlash('errorContent', 'L\'article doit contenir au moins 50 caractères (actuellement '.strlen($formData['content']).').');
            $error = true;
        }

        if ($image !== null && true === $this->checkImageErrors($image)) {
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

    public function createImage($file)
    {
        $image['file'] = $file;
        $image['extension'] = '.' . array_reverse(explode('.', $file['name']))[0];
        $image['name'] = $_SESSION['user']->getId() . time();

        return $image;
    }

    public function checkCommentFormErrors($formData)
    {
        if ($formData['id'] !== $formData['original_id']) {
            $this->addFlash('error', 'Merci de ne pas toucher au DOM !');
            return true;
        }

        foreach ($formData as $data) {
            if (empty($data)) {
                $this->addFlash('error', 'Merci de rentrer tous les champs !');
                return true;
            }
        }

        if (strlen(trim($formData['name'])) < 3) {
            $this->addFlash('error', 'Merci de rentrer un nom correct !');
            return true;
        }

        if (false === filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Merci de rentrer un email valide !');
            return true;
        }

        if (strlen($formData['content']) < 10) {
            $this->addFlash('error', 'Faites un effort... Ce message est trop insignifiant !');
            return true;
        }

        return false;
    }
}
