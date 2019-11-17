<?php

namespace OCR5\Entities;

use DateTime;
use OCR5\Interfaces\EntityInterface;

class Post implements EntityInterface
{
    private $id;
    private $user_id;
    private $username;
    private $title;
    private $content;
    private $chapo;
    private $image;
    private $extension;
    private $status;
    private $added;
    private $updated;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @throws \Exception
     */
    public function getAdded(): string
    {
        return (new DateTime($this->added))->format('d/m/Y à H:i');
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
     * @throws \Exception
     */
    public function getUpdated()
    {
        return ($this->updated !== null) ? (new DateTime($this->updated))->format('d/m/Y à H:i'):null;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public static function getRequestedTraduction()
    {
        return 'article';
    }

    public static function getPublicFields()
    {
        return [
            'username' => 'Pseudo de l\'auteur',
            'title' => 'Titre',
            'chapo' => 'Chapô',
            'added' => 'Date ajout',
            'updated' => 'Dernière modification'
        ];
    }
}