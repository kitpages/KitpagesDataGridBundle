<?php
namespace Kitpages\DataGridBundle\Grid;

use Kitpages\DataGridBundle\Grid\ItemListNormalizer\NormalizerInterface;
use Kitpages\DataGridBundle\Paginator\PaginatorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\QueryBuilder;

use Kitpages\DataGridBundle\Grid\GridConfig;
use Kitpages\DataGridBundle\Grid\Grid;
use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use Kitpages\DataGridBundle\Paginator\Paginator;
use Kitpages\DataGridBundle\Tool\UrlTool;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;
use Kitpages\DataGridBundle\Event\DataGridEvent;

class GridManager
{
    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /**
     * @var PaginatorManager
     */
    protected $paginatorManager;

    /**
     * @var NormalizerInterface
     */
    protected $itemListNormalizer;

    /**
     * @param EventDispatcherInterface                $dispatcher
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        PaginatorManager $paginatorManager,
        NormalizerInterface $itemListNormalizer
    ) {
        $this->dispatcher = $dispatcher;
        $this->paginatorManager = $paginatorManager;
        $this->itemListNormalizer = $itemListNormalizer;
    }

    ////
    // grid methods
    ////
    /**
     * get grid object filled
     *
     * @param  \Doctrine\ORM\QueryBuilder                $queryBuilder
     * @param  \Kitpages\DataGridBundle\Grid\GridConfig $gridConfig
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return \Kitpages\DataGridBundle\Grid\Grid
     */
    public function getGrid(QueryBuilder $queryBuilder, GridConfig $gridConfig, Request $request)
    {
        // create grid objet
        $grid = new Grid();
        $grid->setGridConfig($gridConfig);
        $grid->setUrlTool(new UrlTool());
        $grid->setRequestUri($request->getRequestUri());
        $grid->setDispatcher($this->dispatcher);

        // create base request
        $gridQueryBuilder = clone($queryBuilder);

        // Apply filters
        $filter = $request->query->get($grid->getFilterFormName(),"");
        $this->applyFilter($gridQueryBuilder, $grid, $filter);

        // Apply selector
        $selectorField = $request->query->get($grid->getSelectorFieldFormName(),"");
        $selectorValue = $request->query->get($grid->getSelectorValueFormName(),"");
        $this->applySelector($gridQueryBuilder, $grid, $selectorField, $selectorValue);

        // Apply sorting
        $sortField = $request->query->get($grid->getSortFieldFormName(),"");
        $sortOrder = $request->query->get($grid->getSortOrderFormName(),"");
        $this->applySort($gridQueryBuilder, $grid, $sortField, $sortOrder);

        // build paginator
        $paginatorConfig = $gridConfig->getPaginatorConfig();
        if ($paginatorConfig == null) {
            $paginatorConfig = new PaginatorConfig();
            $paginatorConfig->setCountFieldName($gridConfig->getCountFieldName());
            $paginatorConfig->setName($gridConfig->getName());
        }
        $paginator = $this->paginatorManager->getPaginator($gridQueryBuilder, $paginatorConfig, $request);
        $grid->setPaginator($paginator);

        // calculate limits
        $gridQueryBuilder->setMaxResults($paginator->getPaginatorConfig()->getItemCountInPage());
        $gridQueryBuilder->setFirstResult(($paginator->getCurrentPage()-1) * $paginator->getPaginatorConfig()->getItemCountInPage());

        // send event for changing grid query builder
        $event = new DataGridEvent();
        $event->set("grid", $grid);
        $event->set("gridQueryBuilder", $gridQueryBuilder);
        $event->set("request", $request);
        $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_GET_GRID_QUERY, $event);

        if (!$event->isDefaultPrevented()) {
            // execute request
            $query = $gridQueryBuilder->getQuery();
            $event->set("query", $query);
        }

        $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_GET_GRID_QUERY, $event);

        // hack : recover query from the event so the developper can build a new grid
        // from the gridQueryBuilder in the listener and reinject it in the event.
        $normalizedItemList = $this->itemListNormalizer->normalize(
            $event->get("query"),
            $event->get("gridQueryBuilder")
        );

        // end normalization
        $grid->setItemList($normalizedItemList);
        $grid->setRootAliases($gridQueryBuilder->getRootAliases());

        return $grid;
    }

    protected function applyFilter(QueryBuilder $queryBuilder, Grid $grid, $filter)
    {
        if (!$filter) {
            return;
        }
        $event = new DataGridEvent();
        $event->set("grid", $grid);
        $event->set("gridQueryBuilder", $queryBuilder);
        $event->set("filter", $filter);
        $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_APPLY_FILTER, $event);

        if (!$event->isDefaultPrevented()) {
            $fieldList = $grid->getGridConfig()->getFieldList();
            $filterRequestList = array();
            foreach ($fieldList as $field) {
                if ($field->getFilterable()) {
                    $filterRequestList[] = $queryBuilder->expr()->like($field->getFieldName(), ":filter");
                }
            }
            if (count($filterRequestList) > 0) {
                $reflectionMethod = new \ReflectionMethod($queryBuilder->expr(), "orx");
                $queryBuilder->andWhere($reflectionMethod->invokeArgs($queryBuilder->expr(), $filterRequestList));
                $queryBuilder->setParameter("filter", "%".$filter."%");
            }
            $grid->setFilterValue($filter);
        }
        $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_APPLY_FILTER, $event);
    }

    protected function applySelector(QueryBuilder $queryBuilder, Grid $grid, $selectorField, $selectorValue)
    {
        if (!$selectorField) {
            return;
        }
        $event = new DataGridEvent();
        $event->set("grid", $grid);
        $event->set("gridQueryBuilder", $queryBuilder);
        $event->set("selectorField", $selectorField);
        $event->set("selectorValue", $selectorValue);
        $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_APPLY_SELECTOR, $event);

        if (!$event->isDefaultPrevented()) {
            $queryBuilder->andWhere($selectorField." = :selectorValue");
            $queryBuilder->setParameter("selectorValue", $selectorValue);

            $grid->setSelectorField($selectorField);
            $grid->setSelectorValue($selectorValue);
        }
        $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_APPLY_SELECTOR, $event);
    }

    protected function applySort(QueryBuilder $gridQueryBuilder, Grid $grid, $sortField, $sortOrder)
    {
        if (!$sortField) {
            return;
        }
        $event = new DataGridEvent();
        $event->set("grid", $grid);
        $event->set("gridQueryBuilder", $gridQueryBuilder);
        $event->set("sortField", $sortField);
        $event->set("sortOrder", $sortOrder);
        $this->dispatcher->dispatch(KitpagesDataGridEvents::ON_APPLY_SORT, $event);

        if (!$event->isDefaultPrevented()) {
            $sortFieldObject = null;
            $fieldList = $grid->getGridConfig()->getFieldList();
            foreach ($fieldList as $field) {
                if ($field->getFieldName() == $sortField) {
                    if ($field->getSortable() == true) {
                        $sortFieldObject = $field;
                    }
                    break;
                }
            }
            if (!$sortFieldObject) {
                return;
            }
            if ($sortOrder != "DESC") {
                $sortOrder = "ASC";
            }
            $gridQueryBuilder->orderBy($sortField, $sortOrder);
            $grid->setSortField($sortField);
            $grid->setSortOrder($sortOrder);
        }

        $this->dispatcher->dispatch(KitpagesDataGridEvents::AFTER_APPLY_SORT, $event);
    }

}
