<?php

namespace OCR5\Handler;

use OCR5\App\Session;

class PostHandler extends Handler
{
    protected function getEntity()
    {
        return 'post';
    }

    public function __construct()
    {
        parent::__construct();
        $this->table .= ' INNER JOIN user u ON p.user_id = u.id ';
    }

    public function create($formData)
    {
        return $this->queryDatabase("INSERT INTO post (user_id, title, content, chapo, image, extension, status, added) VALUES (:user_id, :title, :content, :chapo, :image, :extension, 0, NOW())", [
            ':user_id' => Session::get('user')->getId(),
            ':title' => $formData['title'],
            ':content' => $formData['content'],
            ':chapo' => $formData['chapo'],
            ':image' => $formData['img']['name'],
            ':extension' => $formData['img']['extension'],
        ]);
    }

    public function getRequests()
    {
        return $this->select('p.*, u.username', $this->table, 'p.status = 0', null, [], true);
    }

    public function getValids($limit = null, $offset = null, $condition = null, $join = null)
    {
        $andWhere = ($condition !== null) ? ' AND ' . $condition : null;
        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        return $this->select('p.*, u.username', $this->table, 'p.status = 1 ' . $andWhere, $limit, [], true);
    }

    public function getValid($id)
    {
        return $this->select('p.*, u.username', $this->table, 'p.status = 1 AND p.id = :id ', null, [':id' => $id]);
    }

    public function getByUser($id)
    {
        return $this->select('p.*, u.username', $this->table, 'p.status = 1 AND p.user_id = :id', null, [':id' => $id], true);
    }

    public function change($id, $formData, $image)
    {
        return $this->update(
            $this->getEntity(),
            'title = :title, content = :content, chapo = :chapo, image = :image, extension = :extension, updated = NOW()',
            'id = :id',
            [
                ':title' => $formData['title'],
                ':content' => $formData['content'],
                ':chapo' => $formData['chapo'],
                ':image' => $image['name'],
                ':extension' => $image['extension'],
                ':id' => $id,
            ]
        );
    }
}
