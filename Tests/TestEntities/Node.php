<?php
namespace Kitpages\DataGridBundle\Tests\TestEntities;


class Node
{
    protected $id;
    protected $user;
    protected $content;
    protected $createdAt;
    protected $parentId;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}