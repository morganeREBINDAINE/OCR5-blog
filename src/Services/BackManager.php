<?php

namespace OCR5\Services;

class BackManager extends Manager
{
    public function getContributorsRequests()
    {
        return $this->queryDatabase('SELECT * FROM user WHERE status = 0 AND role = "contributor"', [], 'OCR5\Entities\User', true);
    }

    public function getArticlesRequests()
    {
    }

    public function getCommentsRequests()
    {
    }
}
