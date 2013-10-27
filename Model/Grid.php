<?php

namespace Kitpages\DataGridBundle\Model;

use Kitpages\DataGridBundle\Tool\UrlTool;
use Kitpages\DataGridBundle\Model\Field;
use Kitpages\DataGridBundle\DataGridException;
use Kitpages\DataGridBundle\Event\DataGridEvent;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    protected $debugMode = false;
    /** @var EventDispatcherInterface */
    protected $dispatcher = null;
    /** @var string */
    protected $selectorField = null;
    /** @var string */
    protected $selectorValue = null;

    public function __construct()
    {
    }

    public function getSelectorUrl($selectorField, $selectorValue)
    {
        if (!$this->isSelectorSelected($selectorField, $selectorValue)) {
            $uri =  $this->urlTool->changeRequestQueryString(
                $this->requestUri,
                array(
                    $this->getSelectorFieldFormName() => $selectorField,
                    $this->getSelectorValueFormName() => $selectorValue
                )
            );
        } else {
            $uri =  $this->urlTool->changeRequestQueryString(
                $this->requestUri,
                array(
                    $this->getSelectorFieldFormName() => '',
                    $this->getSelectorValueFormName() => ''
                )
            );
        }
        return $uri;
    }

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
            // return null if not found and nullIfNotExists to true
            if ( ( !( is_array($value) && array_key_exists($fieldName, $value) ) ) && $field->getNullIfNotExists() ) {
                $value = null;
                break;
            }
            // get parameter in the $row
            if (!is_array($value)) {
                throw new DataGridException("for key=$fieldName (origin=".$field->getFieldName().") value should be an array and it is not one. value=".print_r($value, true));
            }
            if (!array_key_exists($fieldName, $value)) {
                throw new DataGridException("key=$fieldName (origin=".$field->getFieldName().") not found in array=".print_r($value, true));
            }
            $value = $value[$fieldName];
        }
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
        } else {
            // send event for changing grid query builder
            $event = new DataGridEvent();
            $event->set("value", $value);
            $event->set("row", $row);
            $event->set("field", $field);
            $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_DISPLAY_GRID_VALUE_CONVERSION, $event);
            if (!$event->isDefaultPrevented()) {
                $value = $event->get("value");
                if ($value instanceof \DateTime) {
                    $returnValue = $value->format("Y-m-d H:i:s");
                } else {
                    $returnValue = $value;
                }
                $event->set("returnValue", $returnValue);
            }
            $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_DISPLAY_GRID_VALUE_CONVERSION, $event);
            $returnValue = $event->get("returnValue");
        }
        // auto escape ? (if null, return null, without autoescape...)
        if ($field->getAutoEscape() && !is_null($returnValue)) {
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
    public function getSelectorCssSelected($selectorField, $selectorValue)
    {
        if ($this->isSelectorSelected($selectorField, $selectorValue)) {
            return "kit-grid-selector-selected";
        } else {
            return ;
        }
    }
    public function isSelectorSelected($selectorField, $selectorValue)
    {
        if ($this->getSelectorField() == $selectorField
            && $this->getSelectorValue() == $selectorValue) {
            return true;
        } else {
            return false;
        }
    }
    public function getSelectorFieldFormName()
    {
        return "kitdg_grid_".$this->getGridConfig()->getName()."_selector_field";
    }
    public function getSelectorValueFormName()
    {
        return "kitdg_grid_".$this->getGridConfig()->getName()."_selector_value";
    }

    public function getGridCssName()
    {
        return 'kit-grid-'.$this->getGridConfig()->getName();
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

    public function dump($escape = true)
    {
        $content = print_r($this->itemList, true);
        if ($escape) {
            $content = htmlspecialchars($content);
        }

        $html = '<pre class="kit-grid-debug">';
        $html .= $content;
        $html .= '</pre>';
        return $html;
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
     * @param string $selectorField
     */
    public function setSelectorField($selectorField)
    {
        $this->selectorField = $selectorField;
    }

    /**
     * @return string
     */
    public function getSelectorField()
    {
        return $this->selectorField;
    }

    /**
     * @param string $selectorValue
     */
    public function setSelectorValue($selectorValue)
    {
        $this->selectorValue = $selectorValue;
    }

    /**
     * @return string
     */
    public function getSelectorValue()
    {
        return $this->selectorValue;
    }

    /**
     * @param boolean $debugMode
     */
    public function setDebugMode($debugMode)
    {
        $this->debugMode = $debugMode;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->debugMode;
    }
    /**
     * @param boolean $isDebug
     * @deprecated use setDebugMode instead
     */
    public function setIsDebug($isDebug)
    {
        $this->setDebugMode($isDebug);
        return $this;
    }

    /**
     * @return boolean
     * @deprecated use getDebugMode instead
     */
    public function getIsDebug()
    {
        return $this->getDebugMode();
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

}
