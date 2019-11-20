<?php

namespace OCR5\Traits;

use OCR5\Database\DatabaseMySQL;

trait DatabaseTrait
{
    private $db;

    public function queryDatabase($query, $parameters = [], $className = null, $multiple = false)
    {
//        var_dump($query);
        return $this->db->query($query, $parameters, $className, $multiple);
    }

    public function select($target, $table, $where = null, $limit = null, $params = [], $multiple = false)
    {
        $where = $where ? ' WHERE ' . $where :null;

        $order = $multiple ? ' ORDER BY '.substr($table,0,1).'.id DESC ' : null;

//        var_dump('SELECT '.$target.' FROM '.$table.$where.$order. $limit);die;

        return $this->queryDatabase('SELECT '.$target.' FROM '.$table.$where.$order. $limit, $params, '\OCR5\Entities\\'.ucfirst(explode(' ', $table)[0]), $multiple);
    }

    public function update($table, $fields, $where, $params = [])
    {
        return $this->queryDatabase('UPDATE '.$table.' SET '.$fields.' WHERE '.$where, $params);
    }
}
