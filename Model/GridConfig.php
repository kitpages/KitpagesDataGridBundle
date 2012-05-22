<?php
namespace Kitpages\DataGridBundle\Model;

use Doctrine\ORM\QueryBuilder;

class GridConfig
{
    /** @var string */
    protected $name = "grid";
    /** @var QueryBuilder|null */
    protected $queryBuilder = null;
    /** @var array */
    protected $paginatorConfig = null;
    /** @var array */
    protected $fieldList = array();
    /** @var string */
    protected $countFieldName = null;
    /** @var boolean */
    protected $useGedmoTranslatable = false;


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
    public function setQueryBuilder($queryBuilder)
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
     * @param array $paginatorConfig
     */
    public function setPaginatorConfig($paginatorConfig)
    {
        $this->paginatorConfig = $paginatorConfig;
    }

    /**
     * @return array
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
    
    /**
     * @param boolean $value
     */
    public function useGedmoTranslatable($value)
    {
        $this->useGedmoTranslatable = $value;
    }
    
    /**
     * @return boolean
     */
    public function isUsingGedmoTranslatable()
    {
        return $this->useGedmoTranslatable;
    }

}
