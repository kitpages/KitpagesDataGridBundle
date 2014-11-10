Standalone Paginator
====================

You can use the paginator as a standalone component.

In your controller

```php
<?php

    use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
    [...]

    public function gridAction()
    {
        $gridManager = $this->get("kitpages_data_grid.grid_manager");

        $repository = $this->getDoctrine()->getRepository('KitpagesShopBundle:OrderLine');
        $queryBuilder = $repository->createQueryBuilder("ol");
        $queryBuilder->select("ol");

        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("ol.id");
        $paginatorConfig->setQueryBuilder($queryBuilder);
        $paginator = $gridManager->getPaginator($paginatorConfig, $this->getRequest());
        return $this->render('AppSiteBundle:Default:paginator.html.twig', array(
            "paginator" => $paginator
        ));
    }
```

In your twig :

    {% embed kitpages_data_grid.paginator.default_twig with {'paginator': paginator} %}
    {% endembed %}

