<?php

namespace OCR5\Services;

use OCR5\Traits\DatabaseTrait;
use OCR5\Traits\FlashbagTrait;

class Manager
{
    use FlashbagTrait;
    use DatabaseTrait;

    public function entityExists($entity)
    {
        $fqcn = $this->getEntityFQCN($entity);

        return (class_exists($fqcn)
            && in_array('OCR5\Interfaces\EntityInterface', class_implements($fqcn))
        );
    }

    public function getEntityFQCN($entity)
    {
        return '\OCR5\Entities\\'.ucfirst($entity);
    }

    public function getHandler($entity)
    {
        $handler = '\OCR5\Handler\\'.ucfirst($entity).'Handler';
        return new $handler();
    }
}
