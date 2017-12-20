<?php
namespace Kitpages\DataGridBundle\Tests\TestEntities;


class Node
{
    protected $id;
    protected $user;
    protected $content;
    protected $createdAt;
    protected $parentId;
    protected $subNodeList;
    protected $mainNode;
    protected $assoc;

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setMainNode($mainNode)
    {
        $this->mainNode = $mainNode;
        return $this;
    }

    public function getMainNode()
    {
        return $this->mainNode;
    }

    public function setSubNodeList($subNodeList)
    {
        $this->subNodeList = $subNodeList;
        return $this;
    }

    public function getSubNodeList()
    {
        return $this->subNodeList;
    }

    public function getAssoc()
    {
        return $this->assoc;
    }

    public function setAssoc($assoc)
    {
        $this->assoc = $assoc;
        return $this;
    }
}
