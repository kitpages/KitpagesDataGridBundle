<?php
namespace Kitpages\DataGridBundle;

final class KitpagesDataGridEvents
{
    const ON_GET_GRID_QUERY = "kitpages_data_grid.on_get_grid_query";
    const AFTER_GET_GRID_QUERY = "kitpages_data_grid.after_get_grid_query";

    const ON_GET_PAGINATOR_QUERY = "kitpages_data_grid.on_get_paginator_query";
    const AFTER_GET_PAGINATOR_QUERY = "kitpages_data_grid.after_get_paginator_query";

    const ON_APPLY_FILTER = "kitpages_data_grid.on_apply_filter";
    const AFTER_APPLY_FILTER = "kitpages_data_grid.after_apply_filter";

    const ON_APPLY_SELECTOR = "kitpages_data_grid.on_apply_selector";
    const AFTER_APPLY_SELECTOR = "kitpages_data_grid.after_apply_selector";

    const ON_APPLY_SORT = "kitpages_data_grid.on_apply_sort";
    const AFTER_APPLY_SORT = "kitpages_data_grid.after_apply_sort";

    const ON_DISPLAY_GRID_VALUE_CONVERSION = "kitpages_data_grid.on_display_grid_value_conversion";
    const AFTER_DISPLAY_GRID_VALUE_CONVERSION = "kitpages_data_grid.after_display_grid_value_conversion";
}
