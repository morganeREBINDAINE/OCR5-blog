<?php

namespace OCR5\Services;

use OCR5\App\Post;
use OCR5\App\Session;
use OCR5\Entities\User;
use OCR5\Handler\UserHandler;
use Verot\Upload\Upload;

class FormManager extends Manager
{
    public function checkImageErrors($image)
    {
        if (null === $image['file']) {
            $this->addFlash('errorImage', 'Vous devez ajouter une image à l\'article.');
            return false;
        }
        $handle = new Upload($image['file']);

        if ($handle->uploaded) {
            $handle->allowed = ['image/*'];
            $handle->jpeg_quality=100;
            if ($handle->image_src_y > 2000 || $handle->image_src_x > 2500) {
                $this->addFlash('errorImage', 'L\'image est incorrecte : Largeur maximale de 2500px, hauteur max 2000px.');
                return false;
            }

            if ($handle->image_src_x < 500) {
                $this->addFlash('errorImage', 'L\'image est trop petite : elle doit faire au moins 500px de largeur.');
                return false;
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
                return false;
            }
        }
        return true;
    }

    public function checkPostFormErrors($formData, $image)
    {
        $error = false;

        if ($image === null) {
            $this->addFlash('error', 'Merci de mettre une image.');
            return true;
        }

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

        if (strlen($formData['content']) < 50) {
            $this->addFlash('errorContent', 'L\'article doit contenir au moins 50 caractères (actuellement '.strlen($formData['content']).').');
            $error = true;
        }

        if ($image['status'] === 'new' && false === $this->checkImageErrors($image)) {
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

        if ($result = (new UserHandler())->findStatusContributor($formData['username'], $formData['email'])) {
            switch ($result->getStatus()) {
                case User::STATUS_VALIDATED:
                    $this->addFlash(
                        'error',
                        'Ce contributeur existe déjà.'
                    );
                    break;
                case User::STATUS_REQUEST:
                    $this->addFlash(
                        'error',
                        'Il y déjà une demande d\'inscription pour ce contributeur. Patience ! L\'administratrice va bientôt traiter votre cas.'
                    );
                    break;
            }
            return true;
        }
        if (strlen($formData['password']) < 5) {
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

    public function createImage($file, $post)
    {
        if (Post::get('keep-image') && Post::get('keep-image') === 'on' && $post) {
            $image['extension'] = $post->getExtension();
            $image['name'] = $post->getImage();
            $image['status'] = 'keep';
        } else {
            if ($file['error'] === 4) {
                return null;
            }
            $image['file'] = $file;
            $image['extension'] = '.' . array_reverse(explode('.', $file['name']))[0];
            $image['name'] = Session::get('user')->getId() . time();
            $image['status'] = 'new';
        }

        return $image;
    }

    public function checkCommentFormErrors($formData, $identifier)
    {
        if ($formData['id'] !== $identifier) {
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
            $this->addFlash('error', 'Faites un effort... Ce message est trop court !');
            return true;
        }

        return false;
    }

    public function checkContactForm($formData)
    {
        $error = false;

        if (false === filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'L\'email entré est incorrect.';
        }

        if (strlen($formData['message']) < 30) {
            $error .= ' Le message est trop court (30 caractères min).';
        }

        if (strlen(trim($formData['name'])) < 5) {
            $error .= ' Merci de rentrer un nom correct (5 caractères min).';
        }

        if ($error) {
            $this->addFlash('error', 'Erreur message: '.$error);
        }

        return $error;
    }
}
