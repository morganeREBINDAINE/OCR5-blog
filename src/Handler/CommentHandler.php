<?php

namespace OCR5\Handler;

class CommentHandler extends Handler
{
    protected function getEntity()
    {
        return 'comment';
    }

    public function create($formData)
    {
        return $this->queryDatabase("INSERT INTO comment (post_id, name, email, content, status, added) VALUES (:post, :name, :email, :content, 0, NOW())", [
            ':post' => $formData['id'],
            ':name' => $formData['name'],
            ':email' => $formData['email'],
            ':content' => $formData['content'],
        ]);
    }
}