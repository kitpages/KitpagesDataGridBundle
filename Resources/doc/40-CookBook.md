The CookBook
============

## Use KitpagesDataGridBundle with Twitter Bootstrap

You just have to use the twig embed bootstrap-grid.html.twig instead of grid.html.twig :

```twig
    {% embed '@KitpagesDataGrid/Grid/bootstrap-grid.html.twig' with {'grid': grid} %}
    {% endembed %}
```

You can also change the default embed in the config.yml file.
