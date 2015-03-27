<?php
namespace Kitpages\DataGridBundle\Tests\TestEntities;


use Doctrine\Common\Collections\ArrayCollection;

class NodeAssoc
{
    protected $id;
    protected $name;

    /**
     * @var ArrayCollection $offerList
     *
     */
    protected $nodeList;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addNodeList(\Kitpages\DataGridBundle\Tests\TestEntities\Node $node)
    {
        $this->nodeList[] = $node;

        return $this;
    }

    public function removeNodeList(\Kitpages\DataGridBundle\Tests\TestEntities\Node $node)
    {
        $this->nodeList->removeElement($node);
    }

    public function getNodeList()
    {
        return $this->nodeList;
    }

}
