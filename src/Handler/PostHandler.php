<?php

namespace OCR5\Handler;

// @todo add innerjoin in this.table
class PostHandler extends Handler
{
    protected function getEntity()
    {
        return 'post';
    }

    public function create($formData)
    {
        return $this->queryDatabase("INSERT INTO post (user_id, title, content, chapo, image, extension, status, added) VALUES (:user_id, :title, :content, :chapo, :image, :extension, 0, NOW())", [
            ':user_id' => $_SESSION['user']->getId(),
            ':title' => $formData['title'],
            ':content' => $formData['content'],
            ':chapo' => $formData['chapo'],
            ':image' => $formData['img']['name'],
            ':extension' => $formData['img']['extension'],
        ]);
    }

    public function getRequests()
    {
        $join = ' INNER JOIN user u ON p.user_id = u.id';
        return $this->select('p.*, u.username', $this->table . $join, 'p.status = 0', null, [], true);
    }

    public function getValids($limit = null, $offset = null, $condition = null, $join = null)
    {
        $andWhere = ($condition !== null) ? ' AND ' . $condition : null;
        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        $join = ' INNER JOIN user u ON p.user_id = u.id';
        return $this->select('p.*, u.username', $this->table . $join, 'p.status = 1 ' . $andWhere, $limit, [], true);
    }

    public function getValid($id)
    {
        return $this->queryDatabase('SELECT p.*, u.username FROM post p INNER JOIN user u ON p.user_id = u.id WHERE p.status = 1 AND p.id = :id ', [
            ':id' => $id
        ], 'OCR5\Entities\\'. ucfirst($this->getEntity()));
    }

    public function getByUser($id)
    {
        return $this->select('*', $this->table, 'status = 1 AND user_id = :id', null, [':id' => $id], true);
    }

    public function change($id, $formData, $image)
    {
        return $this->queryDatabase("UPDATE post SET title = :title, content = :content, chapo = :chapo, image = :image, extension = :extension, updated = NOW() WHERE id = :id", [
            ':title' => $formData['title'],
            ':content' => $formData['content'],
            ':chapo' => $formData['chapo'],
            ':image' => $image['name'],
            ':extension' => $image['extension'],
            ':id' => $id,
        ]);
    }
}
