<?php

namespace OCR5\Services;

use OCR5\App\App;
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

    public function getRepository($entity)
    {
        $repository = 'OCR5\Repository\\'.ucfirst($entity).'Repository';
        return new $repository();
    }
}
