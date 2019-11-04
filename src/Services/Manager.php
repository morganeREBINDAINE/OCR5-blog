<?php

namespace OCR5\Services;

use OCR5\App\App;
use OCR5\Traits\FlashbagTrait;

class Manager
{
    use FlashbagTrait;

    private $db;

    public function __construct()
    {
        $this->db = App::getDatabase();
    }

    public function queryDatabase($query, $parameters = [], $className=null, $multiple = false) {
        return $this->db->query($query, $parameters, $className, $multiple);
    }
}