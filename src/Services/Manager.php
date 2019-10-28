<?php

namespace OCR5\Services;

use OCR5\App\AppManager;

class Manager
{
    protected $db;

    public function __construct()
    {
        $this->db = AppManager::getDatabase();
    }
}