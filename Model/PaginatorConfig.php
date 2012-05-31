<?php
namespace Kitpages\DataGridBundle\Model;

use Doctrine\ORM\QueryBuilder;

class PaginatorConfig
{
    /** @var string */
    protected $name = "paginator";
    /** @var QueryBuilder|null */
    protected $queryBuilder = null;
    /** @var int */
    protected $itemCountInPage = 50;
    /** @var int */
    protected $visiblePageCountInPaginator = 5;
    /** @var string */
    protected $countFieldName = null;

    public function getRequestQueryName($key)
    {
        return 'kitdg_paginator_'.$this->getName().'_'.$key;
    }

    /**
     * @param int $itemCountInPage
     */
    public function setItemCountInPage($itemCountInPage)
    {
        $this->itemCountInPage = $itemCountInPage;
    }

    /**
     * @return int
     */
    public function getItemCountInPage()
    {
        return $this->itemCountInPage;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param int $visiblePageCountInPaginator
     */
    public function setVisiblePageCountInPaginator($visiblePageCountInPaginator)
    {
        $this->visiblePageCountInPaginator = $visiblePageCountInPaginator;
    }

    /**
     * @return int
     */
    public function getVisiblePageCountInPaginator()
    {
        return $this->visiblePageCountInPaginator;
    }

    /**
     * @param string $countFieldName
     */
    public function setCountFieldName($countFieldName)
    {
        $this->countFieldName = $countFieldName;
    }

    /**
     * @return string
     */
    public function getCountFieldName()
    {
        return $this->countFieldName;
    }

}
