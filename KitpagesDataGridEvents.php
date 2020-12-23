<?php

namespace Kitpages\DataGridBundle;

use Kitpages\DataGridBundle\Event\AfterApplyFilter;
use Kitpages\DataGridBundle\Event\AfterApplySelector;
use Kitpages\DataGridBundle\Event\AfterApplySort;
use Kitpages\DataGridBundle\Event\AfterDisplayGridValueConversion;
use Kitpages\DataGridBundle\Event\AfterGetGridQuery;
use Kitpages\DataGridBundle\Event\AfterGetPaginatorQuery;
use Kitpages\DataGridBundle\Event\OnApplyFilter;
use Kitpages\DataGridBundle\Event\OnApplySelector;
use Kitpages\DataGridBundle\Event\OnApplySort;
use Kitpages\DataGridBundle\Event\OnDisplayGridValueConversion;
use Kitpages\DataGridBundle\Event\OnGetGridQuery;
use Kitpages\DataGridBundle\Event\OnGetPaginatorQuery;

final class KitpagesDataGridEvents
{
    const ON_GET_GRID_QUERY = OnGetGridQuery::class;
    const AFTER_GET_GRID_QUERY = AfterGetGridQuery::class;

    const ON_GET_PAGINATOR_QUERY = OnGetPaginatorQuery::class;
    const AFTER_GET_PAGINATOR_QUERY = AfterGetPaginatorQuery::class;

    const ON_APPLY_FILTER = OnApplyFilter::class;
    const AFTER_APPLY_FILTER = AfterApplyFilter::class;

    const ON_APPLY_SELECTOR = OnApplySelector::class;
    const AFTER_APPLY_SELECTOR = AfterApplySelector::class;

    const ON_APPLY_SORT = OnApplySort::class;
    const AFTER_APPLY_SORT = AfterApplySort::class;

    const ON_DISPLAY_GRID_VALUE_CONVERSION = OnDisplayGridValueConversion::class;
    const AFTER_DISPLAY_GRID_VALUE_CONVERSION = AfterDisplayGridValueConversion::class;
}
