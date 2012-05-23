Events
======

Usage sample
------------
Let's imagine you want to translate your results with the gedmo translatable. You have to follow these steps :

* create a service that can alter the query
* register this service as a listener of the event "kitpages_data_grid.after_get_grid_query"

Service GridListener.php

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function afterGetGridQuery(DataGridEvent $event)
        {
            $event->get("query")->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );
        }
    }

Configuration to add in config.yml

    services:
        datagrid_listener:
            class: 'App\SiteBundle\EventListener\GridListener'
            tags:
                - { name: "kernel.event_listener", event: "kitpages_data_grid.after_get_grid_query", method: "afterGetGridQuery" }

If you want an event on just one specific datagrid
--------------------------------------------------
You have to name your datagrid in your controller :

    $gridConfig->setName("my-datagrid");

Then in your listener

    public function afterGetGridQuery(DataGridEvent $event)
    {
        $grid = $event->get("grid");
        if ($grid->getGridConfig()->getName() == "my-datagrid") {
            // your specific way
        }
    }


Event list
==========

Events are listed in the file KitpagesDataGridEvents.php. Let's see how to use these events :

kitpages_data_grid.on_get_grid_query
------------------------------------
Allow to modify the gridQueryBuilder before getting the query

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onGetGridQuery(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid");
            $request = $event->get("request");

            // operation
            $query = $gridQueryBuilder->getQuery();
            $event->set("query", $query);

            // cancel default behavior
            $event->preventDefault();
        }
    }

kitpages_data_grid.after_get_grid_query
---------------------------------------
Allow to modify the query before execution

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onGetGridQuery(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid");
            $query = $event->get("query");
            $request = $event->get("request");

            // operation (translate result with gedmo translatable)
            $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );
        }
    }

kitpages_data_grid.on_apply_filter
----------------------------------
modify the way we can apply the filter

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onApplyFilter(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid");
            $filter = $event->get("filter");

            // operation (this is de default behavior, you can code your's here instead)
            $fieldList = $grid->getGridConfig()->getFieldList();
            $filterRequestList = array();
            foreach($fieldList as $field) {
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

            // cancel default behavior
            $event->preventDefault();
        }
    }

kitpages_data_grid.after_apply_filter
----------------------------------
If you want to change something in the gridQueryBuilder after the filter

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function afterApplyFilter(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid");
            $filter = $event->get("filter");

            // change something

        }
    }

kitpages_data_grid.on_apply_sort
----------------------------------
modify the way we can apply the sort

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onApplySort(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid"); // Kitpages\DataGridBundle\Model\Grid object
            $sortField = $event->get("sortField"); // field name for the sort (string)
            $sortOrder = $event->get("sortOrder"); // order ("ASC" or "DESC") of the sort

            // operation (this is de default behavior, you can code your's here instead)
            $sortFieldObject = null;
            $fieldList = $grid->getGridConfig()->getFieldList();
            foreach($fieldList as $field) {
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

            // cancel default behavior
            $event->preventDefault();
        }
    }

kitpages_data_grid.after_apply_sort
----------------------------------
can modify the $queryBuilder after the sort

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function afterApplySort(DataGridEvent $event)
        {
            // data available
            $gridQueryBuilder = $event->get("gridQueryBuilder");
            $grid = $event->get("grid"); // Kitpages\DataGridBundle\Model\Grid object
            $sortField = $event->get("sortField"); // field name for the sort (string)
            $sortOrder = $event->get("sortOrder"); // order ("ASC" or "DESC") of the sort

            // change what you want in the $gridQueryBuilder

        }
    }

kitpages_data_grid.on_get_paginator_query
------------------------------------
Allow to modify the paginatorQueryBuilder before getting the query

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onGetPaginatorQuery(DataGridEvent $event)
        {
            // data available
            $paginatorQueryBuilder = $event->get("paginatorQueryBuilder");
            $paginator = $event->get("paginator");
            $request = $event->get("request");

            // operation
            $query = $paginatorQueryBuilder->getQuery();
            $event->set("query", $query);

            // cancel default behavior
            $event->preventDefault();
        }
    }

kitpages_data_grid.after_get_paginator_query
------------------------------------
Allow to modify the query of the paginator

    <?php
    namespace App\SiteBundle\EventListener;

    use Kitpages\DataGridBundle\Event\DataGridEvent;

    class GridListener
    {
        public function onGetPaginatorQuery(DataGridEvent $event)
        {
            // data available
            $paginatorQueryBuilder = $event->get("paginatorQueryBuilder");
            $paginator = $event->get("paginator");
            $query = $event->get("query");
            $request = $event->get("request");

            // operation : do what you want
        }
    }
