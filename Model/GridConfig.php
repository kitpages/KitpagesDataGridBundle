<?php
namespace Kitpages\DataGridBundle\Model;

use Doctrine\ORM\QueryBuilder;
use Kitpages\DataGridBundle\Model\PaginatorConfig;

class GridConfig
{
    /** @var string */
    protected $name = "grid";
    /** @var QueryBuilder|null */
    protected $queryBuilder = null;
    /** @var PaginatorConfig */
    protected $paginatorConfig = null;
    /** @var array */
    protected $fieldList = array();
    /** @var string */
    protected $countFieldName = null;

    public function addField($field)
    {
        if (is_string($field)) {
            $field = new Field($field);
        }
        $this->fieldList[] = $field;

        return $this;
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
     * @param PaginatorConfig $paginatorConfig
     */
    public function setPaginatorConfig(PaginatorConfig $paginatorConfig)
    {
        $this->paginatorConfig = $paginatorConfig;
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
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;
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
     * @param $name
     * @return Field $field
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
