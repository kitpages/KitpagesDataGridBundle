<?php
namespace Kitpages\DataGridBundle\Paginator;

use Doctrine\ORM\QueryBuilder;

class PaginatorConfig
{
    /** @var string */
    protected $name = 'paginator';

    /** @var QueryBuilder|null */
    protected $queryBuilder = null;

    /** @var int */
    protected $itemCountInPage = null;

    /** @var int */
    protected $visiblePageCountInPaginator = null;

    /** @var string */
    protected $countFieldName = null;

    /**
     * @param string $key
     *
     * @return string
     */
    public function getRequestQueryName($key)
    {
        return 'kitdg_paginator_' . $this->getName() . '_' . $key;
    }

    /**
     * @param int $itemCountInPage
     *
     * @return PaginatorConfig Fluent interface
     */
    public function setItemCountInPage($itemCountInPage)
    {
        $this->itemCountInPage = $itemCountInPage;

        return $this;
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
     *
     * @return PaginatorConfig Fluent interface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     *
     * @return PaginatorConfig Fluent interface
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
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
     *
     * @return PaginatorConfig Fluent interface
     */
    public function setVisiblePageCountInPaginator($visiblePageCountInPaginator)
    {
        $this->visiblePageCountInPaginator = $visiblePageCountInPaginator;

        return $this;
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
     *
     * @return PaginatorConfig Fluent interface
     */
    public function setCountFieldName($countFieldName)
    {
        $this->countFieldName = $countFieldName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountFieldName()
    {
        return $this->countFieldName;
    }
}