Grid Extended Use
=================

More than one Grids in one page
-------------------------------
If you have more than one grid on a page, you have to name each grid with a different name. In your controller

    $gridConfig->setName("first-grid");

Change Paginator parameters of your grid
----------------------------------------
If you want to change the result number on each page for example.

    public function gridAction()
    {
        $gridManater = $this->get("kitpages_data_grid.manager");

        $repository = $this->getDoctrine()->getRepository('KitpagesShopBundle:OrderLine');
        $queryBuilder = $repository->createQueryBuilder('ol');
        $queryBuilder->select('ol');

        $gridConfig = new GridConfig();
        $gridConfig
            ->setCountFieldName('ol.id')
            ->addField(new Field('ol.id', array('sortable' => true)))
            ->addField(new Field('ol.shopReference', array(
                'label' => 'Ref',
                'filterable' => true,
                'sortable' => true
            )))
            ->addField(new Field('ol.updatedAt', array(
                'sortable' => true,
                'formatValueCallback' => function ($value) { return $value->format('Y/m/d'); }
            )))
        ;

        // paginator configuration
        $gridPaginatorConfig = new PaginatorConfig()
        $gridPaginatorConfig
            ->setName($gridConfig->getName())
            ->setCountFieldName($gridConfig->getCountFieldName())
            ->setItemCountInPage(10)
        ;
        $gridConfig->setPaginatorConfig($gridPaginatorConfig);

        $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

        return $this->render('AppSiteBundle:Default:grid.html.twig', array(
            'grid' => $grid
        ));
    }

Select recursive field
----------------------
If you have for exemple a categorie table which as a recursion with parent_id, you must use 
the leftJoin() condition in your queryBuilder.

    public function gridAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select(array('c', 'cp'))
            ->from('My\SiteBundle\Entity\Categorie', 'c')
            ->leftJoin('c.idParent', 'p')
        ;

        $gridConfig = new GridConfig();
        $gridConfig
            ->setCountFieldName('c.id')
            ->addField(new Field('c.foo')
            ->addField(new Field('c.idParent', array(
                'label' => 'Parent',
                'formatValueCallback' => function($value) { return (!empty($value)) ? $value['foo'] : 'None'; }
            )))
        ;

        $gridManager = $this->get('kitpages_data_grid.manager');
        $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

        return $this->render('MySiteBundle:Default:grid.html.twig', array(
            'grid' => $grid
        ));
    }

formatValueCallback
-------------------
If you want to format a data, you can use a simple callback. For example, if a data is a dateTime, you can format
use that code :

    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            'label' => 'Updated at',
            'sortable'=>true,
            'formatValueCallback' => function($value) { return $value->format('Y/m/d'); }
        )
    ));

You can also have a second argument in your callback that will receive the entire row received from the query.

    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            'label' => 'Date and Id',
            'sortable' => true,
            'formatValueCallback' => function($value, $row) { return $value->format('Y/m/d') . '--' . $row['id']; }
        )
    ));

Grid with a "GROUP BY" querybuilder
-----------------------------------
For group by queries, watch out for the count field name you define. In the count query
for the paginator, the groupBy part is removed form the queryBuilder and a distinct is
added before the count field.

    // create query builder
    $repository = $this->getDoctrine()->getRepository('AcmeStoreBundle:Product');
    $queryBuilder = $repository->createQueryBuilder("item")
        ->select('item.type, count(item.id) as cnt')
        ->groupBy('item.type')
        ->where('item.price > :price')
        ->setParameter('price', '19.90')
    ;

    $gridConfig = new GridConfig();
    $gridConfig
        ->setCountFieldName('item.type')
        ->addField(new Field('item.type'))
        ->addField(new Field('cnt'))
    ;



Render the item value with a twig template
------------------------------------------

You can format a data with a twig template by using the callback system :

    // get templating service
    $twig = $this->get('templating');
    // add the field
    $gridConfig->addField(new Field(
        'ol.updatedAt',
        array(
            "formatValueCallback"=>function($value, $row) use ($twig) {
                return $twig->render("AppSiteBundle:Default:grid-element.html.twig", $row);
            },
            "autoEscape" => false
        )
    ));

And the twig file could be grid-element.html.twig in the right bundle :

    <strong>
      id={{id}}<br/>
      date={{updatedAt | date ("Y/m/d") }}
    </strong>


Add multiple action with checkboxes on the left
-----------------------------------------------

You can add a form around the datagrid and add any input elements in the datagrid just by extending
the embeded twig.

Let's see in this example how to add checkboxes on the left of the grid for multiple actions.

    {% embed 'KitpagesDataGridBundle:Grid:grid.html.twig' with {'grid': grid} %}

        {% block kit_grid_before_table %}
            <form action="{{ path('my_route') }}" method="POST">
        {% endblock %}

        {% block kit_grid_thead_before_column %}
            <th>select</th>
        {% endblock %}

        {% block kit_grid_tbody_before_column %}
            <td><input type="checkbox" name="check_{{ item.id }}" /></td>
        {% endblock %}

        {% block kit_grid_after_table %}
            <input type="submit" />
            </form>
        {% endblock %}

    {% endembed %}

You can see all the extension points of the data grid template in the
file [views/Grid/grid.html.twig](https://github.com/kitpages/KitpagesDataGridBundle/blob/master/Resources/views/Grid/grid.html.twig)
