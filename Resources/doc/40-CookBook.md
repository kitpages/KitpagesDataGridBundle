The CookBook
============

## Use KitpagesDataGridBundle with Twitter Bootstrap

You just have to use the twig embed bootstrap-grid.html.twig instead of grid.html.twig :

```twig
    {% embed 'KitpagesDataGridBundle:Grid:bootstrap-grid.html.twig' with {'grid': grid} %}
    {% endembed %}
```

