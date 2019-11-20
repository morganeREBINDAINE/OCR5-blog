<?php

namespace OCR5\Handler;

use OCR5\Database\DatabaseMySQL;
use OCR5\Traits\DatabaseTrait;

abstract class Handler
{
    use DatabaseTrait;

    protected $table;

    abstract protected function getEntity();
    abstract public function create($formData);

    public function __construct()
    {
        $this->db = new DatabaseMySQL();
        $this->table = $this->getEntity() . ' ' . substr($this->getEntity(), 0, 1);
    }

    public function getValids($limit = null, $offset = null, $condition = null, $join = null)
    {
        $andWhere = ($condition !== null) ? ' AND ' . $condition : null;
        $tableLetter = substr($this->getEntity(), 0, 1);

        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        return $this->select('*', $this->table . $join, $tableLetter. '.status = 1 ' . $andWhere, $limit, [], true);
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

    public function countValids() {
        $result = $this->queryDatabase('SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE status = 1')['count'];

        return $result;
    }

    public function get($id) {
        $tableLetter = substr($this->getEntity(), 0, 1);

        return $this->select('*', $this->table, $tableLetter.'.id = :id', null, [
            ':id' => $id
        ]);
    }

    public function changeStatus($id, $status)
    {
        if (false === in_array($status, [1,2,3])) {
            return null;
        }
        $entity = $this->getEntity();

        return $this->queryDatabase('UPDATE '.$entity. ' SET status = '.$status.' WHERE id = :id', [
            ':id' => $id
        ]);
    }
}
