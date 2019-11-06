<?php

namespace OCR5\Services;

class EntityManager extends Manager
{
    public function handleContributor($id, $status)
    {
        return $this->queryDatabase('UPDATE user SET status = :status WHERE id = :id', [
            'status' => $status,
            ':id' => $id,
        ]);
    }

    public function createPost($formData, $image)
    {
        return $this->queryDatabase("INSERT INTO post (user_id, title, content, chapo, image, status, added) VALUES (:user_id, :title, :content, :chapo, :image, 0, NOW())", [
            ':user_id' => $_SESSION['user']->getId(),
            ':title' => $formData['title'],
            ':chapo' => $formData['chapo'],
            ':image' => $image,
            ':content' => $formData['content']
        ]);
    }

    public function createContributor($formData)
    {
        return $this->queryDatabase("INSERT INTO user (username, email, password, role, status, added) VALUES (:username, :email, :password, 'contributor', 0, NOW())", [
            ':username' => $formData['username'],
            ':password' => password_hash($formData['password'], PASSWORD_DEFAULT),
            ':email' => $formData['email']
        ]);
    }
}