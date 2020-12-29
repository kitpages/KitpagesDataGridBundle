<?php

namespace Kitpages\DataGridBundle\Grid;

use Doctrine\ORM\QueryBuilder;
use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use SebastianBergmann\CodeCoverage\Driver\Selector;

class GridConfig
{
    /** @var string */
    protected $name = 'grid';

    /** @var QueryBuilder|null */
    protected $queryBuilder = null;

    /** @var PaginatorConfig */
    protected $paginatorConfig = null;

    /** @var array|Field[] */
    protected $fieldList = array();

    /** @var array|Selector[] */
    protected $selectorList = array();

    /** @var string */
    protected $countFieldName = null;

    /**
     * @param Field|string  $field
     * @param array         $options
     * @param string[] list of tags
     *
     * @return GridConfig Fluent interface
     */
    public function addField($field, $options = array(), $tagList = array())
    {
        if (! (\is_string($field) || $field instanceof Field)) {
            throw new \InvalidArgumentException('Argument $field should be string or instance of Kitpages\DataGridBundle\Grid\Field');
        }

        if (\is_string($field)) {
            $field = new Field($field, $options, $tagList);
        }

        $this->fieldList[] = $field;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return GridConfig Fluent interface
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
     * @return GridConfig Fluent interface
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
     * @param PaginatorConfig $paginatorConfig
     *
     * @return GridConfig Fluent interface
     */
    public function setPaginatorConfig(PaginatorConfig $paginatorConfig)
    {
        $this->paginatorConfig = $paginatorConfig;

        return $this;
    }

    /**
     * @return PaginatorConfig
     */
    public function getPaginatorConfig()
    {
        return $this->paginatorConfig;
    }

    /**
     * @param array $fieldList
     *
     * @return GridConfig Fluent interface
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * returns the field corresponding to the name
     *
     * @param string $name
     *
     * @return Field|null $field
     */
    public function getFieldByName($name)
    {
        foreach ($this->fieldList as $field) {
            if ($field->getFieldName() === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Returns a list of fields that contains the given $tag.
     *
     * @param $tag
     * @return Field[]
     */
    public function getFieldListByTag($tag)
    {
        $matchingFieldList = array();
        foreach ($this->fieldList as $field) {
            if ($field->hasTag($tag)) {
                $matchingFieldList[]  = $field;
            }
        }
        return $matchingFieldList;
    }

    /**
     * @param string $countFieldName
     *
     * @return GridConfig Fluent interface
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

    /**
     * @param array
     *
     * @return GridConfig Fluent interface
     */
    public function addSelector($selector)
    {
        $this->selectorList[] = $selector;

        return $this;
    }

    /**
     * @return array
     */
    public function getSelectorList()
    {
        return $this->selectorList;
    }

    /**
     * @param array $selectorList
     *
     * @return GridConfig Fluent interface
     */
    public function setSelectorList($selectorList)
    {
        $this->selectorList = $selectorList;

        return $this;
    }


}
