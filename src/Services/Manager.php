<?php

namespace OCR5\Services;

use OCR5\App\AppManager;
use OCR5\Traits\FlashbagTrait;

class Manager
{
    use FlashbagTrait;

    private $db;

    public function __construct()
    {
        $this->db = AppManager::getDatabase();
    }

    public function queryDatabase($query, $parameters = [], $multiple = false) {
        return $this->db->query($query, $parameters, $multiple);
    }
}