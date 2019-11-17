<?php

namespace OCR5\Repository;

class PostRepository extends Repository
{
    protected function getEntity() {
        return 'post';
    }

    public function getValid($id)
    {
        return $this->queryDatabase('SELECT * FROM post p INNER JOIN user u ON p.user_id = u.id WHERE p.status = 1 AND p.id = :id ', [
            ':id' => $id
        ], 'OCR5\Entities\\'. ucfirst($this->getEntity()));
    }
}