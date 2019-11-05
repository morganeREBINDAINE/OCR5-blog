<?php

namespace OCR5\Services;

class ContributorsManager extends Manager
{
    public function getValidsContributors()
    {
        return $this->queryDatabase('SELECT * FROM user WHERE role = "contributor" AND status = 1', [], 'OCR5\Entities\User', true);
    }

    public function handleContributor($id, $status)
    {
        return $this->queryDatabase('UPDATE user SET status = :status WHERE id = :id', [
            'status' => $status,
            ':id' => $id,
        ]);
    }
}