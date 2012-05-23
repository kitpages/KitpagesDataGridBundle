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
        $queryBuilder = $repository->createQueryBuilder("ol");
        $queryBuilder->select("ol");

        $gridConfig = new GridConfig();
        $gridConfig->setCountFieldName("ol.id");
        $gridConfig->addField(new Field("ol.id", array("sortable"=>true)));
        $gridConfig->addField(new Field("ol.shopReference", array(
            "label" => "Ref",
            "filterable"=>true,
            "sortable" => true
        )));
        $gridConfig->addField(new Field(
            "ol.updatedAt",
            array(
                "sortable"=>true,
                "formatValueCallback" => function ($value) { return $value->format("Y/m/d"); }
            )
        ));

        // paginator configuration
        $gridPaginatorConfig = new PaginatorConfig();
        $gridPaginatorConfig->setName($gridConfig->getName());
        $gridPaginatorConfig->setCountFieldName($gridConfig->getCountFieldName());
        $gridPaginatorConfig->setItemCountInPage(10);
        $gridConfig->setPaginatorConfig($gridPaginatorConfig);

        $grid = $gridManater->getGrid($queryBuilder, $gridConfig, $this->getRequest());

        return $this->render('AppSiteBundle:Default:grid.html.twig', array(
            "grid" => $grid
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
        $queryBuilder->select(array('c', 'cp'))
                ->from('My\SiteBundle\Entity\Categorie', 'c')
                ->leftJoin('c.idParent', 'p');

        $gridConfig = new GridConfig();
        $gridConfig->setCountFieldName("c.id");
        $gridConfig->addField(new Field("c.foo");
        $gridConfig->addField(new Field("c.idParent", array(
            "label" => "Parent",
            "formatValueCallback" => function($value) { return (!empty($value)) ? $value['foo'] : 'None'; }
        )));

        $gridManager = $this->get("kitpages_data_grid.manager");
        $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

        return $this->render('MySiteBundle:Default:grid.html.twig', array(
            "grid" => $grid
        ));
    }
