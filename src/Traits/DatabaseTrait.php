<?php

namespace OCR5\Traits;

use OCR5\Database\DatabaseMySQL;

trait DatabaseTrait
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseMySQL();
    }

    public function queryDatabase($query, $parameters = [], $className = null, $multiple = false)
    {
        return $this->db->query($query, $parameters, $className, $multiple);
    }

    public function select($target, $table, $where = null, $limit = null, $params = [], $multiple = false)
    {
        $where = $where ? ' WHERE ' . $where :null;

        return $this->queryDatabase('SELECT '.$target.' FROM '.$table.$where.' ORDER BY id DESC ' . $limit, $params, '\OCR5\Entities\\'.ucfirst($table), $multiple);
    }

    public function update($table, $fields, $where, $params = [])
    {
        return $this->queryDatabase('UPDATE '.$table.' SET '.$fields.' WHERE '.$where, $params);
    }
}
