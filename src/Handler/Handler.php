<?php

namespace OCR5\Handler;

use OCR5\Database\DatabaseMySQL;
use OCR5\Traits\DatabaseTrait;

abstract class Handler
{
    use DatabaseTrait;

    protected $table;
    protected $letter;

    abstract protected function getEntity();
    abstract public function create($formData);

    public function __construct()
    {
        $this->db = new DatabaseMySQL();
        $this->letter = substr($this->getEntity(), 0, 1);
        $this->table  = $this->getEntity() . ' ' . $this->letter;
    }

    public function getValids($limit = null, $offset = null, $condition = null, $join = null)
    {
        $andWhere = ($condition !== null) ? ' AND ' . $condition : null;

        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        return $this->select('*', $this->table . $join, $this->letter. '.status = 1 ' . $andWhere, $limit, [], true);
    }

    public function getValid($id)
    {
        return $this->select('*', $this->table, 'status = 1 AND id = :id ', null, [
            ':id' => $id
        ]);
    }

    public function getRequests()
    {
        return $this->select('*', $this->table, 'status = 0', null, [], true);
    }

    public function countValids()
    {
        return $this->queryDatabase('SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE '.$this->letter.'.status = 1')['count'];
    }

    public function get($id)
    {
        return $this->select('*', $this->table, $this->letter.'.id = :id', null, [
            ':id' => $id
        ]);
    }

    public function changeStatus($id, $status)
    {
        if (false === in_array($status, [1,2,3])) {
            return null;
        }

        return $this->update($this->table, $this->letter.'.status = :status', $this->letter.'.id = :id', [
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
