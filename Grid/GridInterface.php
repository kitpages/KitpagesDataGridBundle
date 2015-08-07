<?php

namespace Kitpages\DataGridBundle\Grid;

use Kitpages\DataGridBundle\Tool\UrlTool;
use Kitpages\DataGridBundle\Grid\Field;
use Kitpages\DataGridBundle\DataGridException;
use Kitpages\DataGridBundle\Event\DataGridEvent;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface GridInterface
{
    public function getSelectorUrl($selectorField, $selectorValue);
    public function getSortUrl($fieldName);
    public function getSortCssClass($fieldName);
    public function displayGridValue($row, Field $field);
    public function getFilterFormName();
    public function getSortFieldFormName();
    public function getSortOrderFormName();
    public function getSelectorCssSelected($selectorField, $selectorValue);
    public function isSelectorSelected($selectorField, $selectorValue);
    public function getSelectorFieldFormName();
    public function getSelectorValueFormName();
    public function getGridCssName();
    public function setGridConfig($gridConfig);
    public function getGridConfig();
    public function setItemList($itemList);
    public function getItemList();
    public function dump($escape = true);
    public function setPaginator($paginator);
    public function getPaginator();
    public function setUrlTool($urlTool);
    public function getUrlTool();
    public function setRequestUri($requestUri);
    public function getRequestCurrentRoute();
    public function setRequestCurrentRoute($requestCurrentRoute);
    public function getRequestCurrentRouteParams();
    public function setRequestCurrentRouteParams($requestCurrentRouteParams);
    public function getRequestUri();
    public function setFilterValue($filterValue);
    public function getFilterValue();
    public function setSortField($sortField);
    public function getSortField();
    public function setSortOrder($sortOrder);
    public function getSortOrder();
    public function setSelectorField($selectorField);
    public function getSelectorField();
    public function setSelectorValue($selectorValue);
    public function getSelectorValue();
    public function setDebugMode($debugMode);
    public function getDebugMode();
    public function setDispatcher(EventDispatcherInterface $dispatcher);
    public function getDispatcher();
}
