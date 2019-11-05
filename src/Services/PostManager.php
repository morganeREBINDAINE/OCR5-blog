<?php

namespace OCR5\Services;

class PostManager extends Manager
{
    public function createPost($formData)
    {
        return $this->queryDatabase("INSERT INTO post (user_id, title, content, chapo, status, added) VALUES (:user_id, :title, :content, :chapo, 0, NOW())", [
            ':user_id' => $_SESSION['user']->getId(),
            ':title' => $formData['title'],
            ':chapo' => $formData['chapo'],
            ':content' => $formData['content']
        ]);
    }

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
}
