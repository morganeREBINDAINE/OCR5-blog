<?php

namespace OCR5\Repository;

use OCR5\Traits\DatabaseTrait;

abstract class Repository
{
    use DatabaseTrait;

    abstract protected function getEntity();

    public function getValids($limit = null, $offset = null, $condition = null)
    {
        $andWhere = ($condition !== null) ? ' AND ' . $condition : null;

        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;


        return $this->select('*', $this->getEntity(), 'status = 1 ' . $andWhere, $limit, [], true);
    }

    public function getValid($id)
    {
        return $this->select('*', $this->getEntity(), 'status = 1 AND id = :id ', null, [
            ':id' => $id
        ]);
    }
}
