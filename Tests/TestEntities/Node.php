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

    public function setMainNode($mainNode)
    {
        $this->mainNode = $mainNode;
    }

    public function getMainNode()
    {
        return $this->mainNode;
    }

    public function setSubNodeList($subNodeList)
    {
        $this->subNodeList = $subNodeList;
    }

    public function getSubNodeList()
    {
        return $this->subNodeList;
    }


}