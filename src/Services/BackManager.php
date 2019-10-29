<?php

namespace OCR5\Services;

class BackManager extends Manager
{
    public function getContributorsRequests()
    {
        return $this->queryDatabase('SELECT * FROM user WHERE status = 0', [], true);
    }

    public function getArticlesRequests()
    {
    }

    public function getCommentsRequests()
    {
    }
}
