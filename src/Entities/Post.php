<?php

namespace OCR5\Entities;

use DateTime;
use OCR5\Interfaces\EntityInterface;

class Post implements EntityInterface
{
    private $id;
    private $user_id;
    private $title;
    private $content;
    private $chapo;
    private $image;
    private $extension;
    private $status;
    private $added;
    private $updated;

    const REQUESTED_TRADUCTION = 'article';
    const PUBLIC_FIELDS        = [
        'user' => 'Pseudo de l\'auteur',
        'title' => 'Titre',
        'chapo' => 'Chapô',
        'added' => 'Date ajout',
        'updated' => 'Dernière modification'
    ];

    public function __construct()
    {
        $this->status = 0;
        $this->added = (new DateTime())->format('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user_id = $user;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param mixed $chapo
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getAdded(): string
    {
        return $this->added;
    }

    /**
     * @param string $added
     */
    public function setAdded(string $added)
    {
        $this->added = $added;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getRequestedTraduction()
    {
        return self::REQUESTED_TRADUCTION;
    }

    public function getPublicFields()
    {
        return self::PUBLIC_FIELDS;
    }
}