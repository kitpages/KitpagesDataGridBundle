<?php

namespace Kitpages\DataGridBundle\Model;

use Kitpages\DataGridBundle\Tool\UrlTool;
use Kitpages\DataGridBundle\Model\Field;
use Kitpages\DataGridBundle\DataGridException;

class Grid
{
    /** @var Paginator */
    protected $paginator = null;
    /** @var GridConfig */
    protected $gridConfig = null;
    /** @var array */
    protected $itemList = array();
    /** @var UrlTool */
    protected $urlTool = null;
    /** @var string */
    protected $requestUri = null;
    /** @var string */
    protected $filterValue = null;
    /** @var string */
    protected $sortField = null;
    /** @var string */
    protected $sortOrder = null;
    /** @var array ($gridQueryBuilder->getRootAliases()) */
    protected $rootAliases = array();
    /** @var bool */
    protected $isDebug = false;

    public function getSortUrl($fieldName)
    {
        $uri =  $this->urlTool->changeRequestQueryString(
            $this->requestUri,
            $this->getSortFieldFormName(),
            $fieldName
        );
        if ($fieldName == $this->getSortField()) {
            $order = ($this->getSortOrder() == "ASC") ? "DESC" : "ASC";
        } else {
            $order = "ASC";
        }

        return $this->urlTool->changeRequestQueryString(
            $uri,
            $this->getSortOrderFormName(),
            $order
        );
    }
    public function getSortCssClass($fieldName)
    {
        $css = "";
        if ($fieldName == $this->getSortField()) {
            $css .= " kit-grid-sort ";
            $css .= " kit-grid-sort-".strtolower($this->getSortOrder())." ";
        }

        return $css;
    }

    public function displayGridValue($row, Field $field)
    {
        // parse field name and get value after the dot
        $fieldNameTab = explode('.', $field->getFieldName());

        if ( in_array($fieldNameTab[0], $this->rootAliases) ) {
            array_shift($fieldNameTab);
        }

        $value = $row;
        while (count($fieldNameTab) > 0) {
            $fieldName = array_shift($fieldNameTab);
            // get parameter in the $row
            $value = $row[$fieldName];
        }

//        $fieldName = array_shift($fieldNameTab);
//        $value = $row[$fieldNameTab];

        // real treatment
        if ( is_callable( $field->getFormatValueCallback() ) ) {
            $callback = $field->getFormatValueCallback();
            $reflection = new \ReflectionFunction($callback);
            if ($reflection->getNumberOfParameters() == 1) {
                $returnValue =  $callback($value);
            } elseif ($reflection->getNumberOfParameters() == 2) {
                $returnValue =  $callback($value, $row);
            } else {
                throw new DataGridException("Wrong number of parameters in the callback for field ".$field->getFieldName());
            }
        } elseif (is_scalar($value)) {
            $returnValue = $value;
        } elseif ($value instanceof \DateTime) {
            $returnValue = $value->format("Y-m-d H:i:s");
        } else {
            $returnValue = $value;
        }
        // auto escape ?
        if ($field->getAutoEscape()) {
            $returnValue = htmlspecialchars($returnValue);
        }

        return $returnValue;
    }

    public function getFilterFormName()
    {
        return "kitdg_grid_".$this->getGridConfig()->getName()."_filter";
    }
    public function getSortFieldFormName()
    {
        return "kitdg_grid_".$this->getGridConfig()->getName()."_sort_field";
    }
    public function getSortOrderFormName()
    {
        return "kitdg_grid_".$this->getGridConfig()->getName()."_sort_order";
    }

    /**
     * @param \Kitpages\DataGridBundle\Model\GridConfig $gridConfig
     */
    public function setGridConfig($gridConfig)
    {
        $this->gridConfig = $gridConfig;
    }

    /**
     * @return \Kitpages\DataGridBundle\Model\GridConfig
     */
    public function getGridConfig()
    {
        return $this->gridConfig;
    }

    public function setItemList($itemList)
    {
        $this->itemList = $itemList;
    }

    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * @param array $rootAliases
     */
    public function setRootAliases($rootAliases)
    {
        $this->rootAliases = $rootAliases;
    }

    /**
     * @param \Kitpages\DataGridBundle\Model\Paginator $paginator
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @return \Kitpages\DataGridBundle\Model\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param \Kitpages\DataGridBundle\Tool\UrlTool $urlTool
     */
    public function setUrlTool($urlTool)
    {
        $this->urlTool = $urlTool;
    }

    /**
     * @return \Kitpages\DataGridBundle\Tool\UrlTool
     */
    public function getUrlTool()
    {
        return $this->urlTool;
    }

    /**
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @param string $filterValue
     */
    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;
    }

    /**
     * @return string
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * @param string $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param boolean $isDebug
     */
    public function setIsDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @return boolean
     */
    public function getIsDebug()
    {
        return $this->isDebug;
    }


}
