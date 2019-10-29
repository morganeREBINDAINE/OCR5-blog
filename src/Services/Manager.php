<?php

namespace OCR5\Services;

use OCR5\App\AppManager;
use OCR5\Traits\FlashbagTrait;

class Manager
{
    use FlashbagTrait;

    protected $db;

    public function __construct()
    {
        $this->db = AppManager::getDatabase();
    }
}