<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Doctrine\ORM\EntityManager;
use Kitpages\DataGridBundle\Grid\Field;
use Kitpages\DataGridBundle\Grid\ItemListNormalizer\LegacyNormalizer;
use Kitpages\DataGridBundle\Grid\ItemListNormalizer\StandardNormalizer;
use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use Kitpages\DataGridBundle\Grid\GridConfig;
use Kitpages\DataGridBundle\Grid\GridManager;
use Kitpages\DataGridBundle\Paginator\PaginatorManager;
use Kitpages\DataGridBundle\Tests\BundleOrmTestCase;

/**
 * @group GridManagerTest
 */
class GridManagerTest extends BundleOrmTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function initGridConfig()
    {
        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.id");
        $paginatorConfig->setItemCountInPage(3);

        $gridConfig = new GridConfig();
        $gridConfig->setPaginatorConfig($paginatorConfig);
        $gridConfig->setCountFieldName("node.id");
        $gridConfig
            ->addField(new Field("node.id"))
            ->addField(new Field("node.createdAt",
            array(
                "sortable"=>true,
                "formatValueCallback" => function ($value) { return $value->format("Y/m/d"); }
            )
        ));
        $gridConfig->addField(new Field("node.content",
            array(
                "formatValueCallback" => function ($value, $row) { return $value.":".$row["createdAt"]->format("Y"); }
            )
        ));
        $gridConfig->addField(new Field("node.user", array(
            "filterable"=> true
        )));
        return $gridConfig;
    }

    public function getGridManager()
    {
        // create EventDispatcher mock
        $service = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $parameters = array(
            "default_twig" => "toto.html.twig",
            "item_count_in_page" => 50,
            "visible_page_count_in_paginator" => 5
        );

        // normalizer
        $normalizer = new StandardNormalizer();

        $gridManager = new GridManager($service, new PaginatorManager($service, $parameters), $normalizer, '\Kitpages\DataGridBundle\Hydrators\DataGridHydrator', '\Kitpages\DataGridBundle\Grid\Grid');
        return $gridManager;

    }


    public function testGridBasic()
    {
        // create Request mock (ok this is not a mock....)
        $_SERVER["REQUEST_URI"] = "/foo";
        $request = new \Symfony\Component\HttpFoundation\Request();
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        $gridConfig = new GridConfig();
        $gridConfig->setCountFieldName("node.id");
        $gridConfig->addField(new Field("node.createdAt",
            array(
                "sortable"=>true,
                "formatValueCallback" => function ($value) { return $value->format("Y/m/d"); }
            )
        ));
        $gridConfig->addField(new Field("node.content",
            array(
                "formatValueCallback" => function ($value, $row) { return $value.":".$row["node.createdAt"]->format("Y"); }
            )
        ));

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(11, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 11 , count($itemList));
        $this->assertEquals( 1 , $itemList[0]["node.id"]);
        // simple callback
        $this->assertEquals( "2010/04/24" , $grid->displayGridValue($itemList[0], $gridConfig->getFieldByName("node.createdAt")));
        $this->assertEquals( "foobar:2010" , $grid->displayGridValue($itemList[0], $gridConfig->getFieldByName("node.content")));
    }

    public function testGridRelation()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_paginator_paginator_currentPage", 2);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node, node.id*2 as doubleId");

        $gridConfig = $this->initGridConfig();
        $gridConfig->addField(new Field("doubleId"));

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(11, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 3 , count($itemList));
        $this->assertEquals( "paginator", $paginator->getPaginatorConfig()->getName());
        $this->assertEquals( 2, $paginator->getCurrentPage() );
        $this->assertEquals( 1, $paginator->getPreviousButtonPage());
        $this->assertEquals( 3, $paginator->getNextButtonPage());
        $this->assertEquals( 10 , $itemList[1]["doubleId"]);
        // simple callback
    }

    /*
     * Test added following this issue : https://github.com/kitpages/KitpagesDataGridBundle/issues/18
     * But I can't reproduce that bug...
     * TODO: go back here later and reproduce this issue...
     */
    /**
     * @group testDQL
     */
    public function testGridLeftJoin()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_paginator_paginator_currentPage", 2);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("DISTINCT node.id as gouglou, node, count(sn.id) as intervals")
            ->leftJoin('node.subNodeList', 'sn')
            ->groupBy('node.id')
            ->orderBy('node.id', 'ASC');

        $gridConfig = $this->initGridConfig();
        $gridConfig->addField(new Field("doubleId"));

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid( $gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(11, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 3 , count($itemList));
        $this->assertEquals( "paginator", $paginator->getPaginatorConfig()->getName());
        $this->assertEquals( 2, $paginator->getCurrentPage() );
        $this->assertEquals( 1, $paginator->getPreviousButtonPage());
        $this->assertEquals( 3, $paginator->getNextButtonPage());
        // simple callback
    }

    /**
     */
    public function testGridLeftJoinGroupBy()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_paginator_paginator_currentPage", 1);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        $em = $this->getEntityManager();

        // create queryBuilder
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node, assoc, count(sn.id) as intervals")
            ->leftJoin('node.assoc', 'assoc')
            ->leftJoin('node.subNodeList', 'sn')
            ->groupBy('node.id')
            ->where('node.id = 11')
            ->orderBy('node.id', 'ASC');

        $gridConfig = $this->initGridConfig();
        $gridConfig->addField(new Field("assoc.id"));

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid( $gridConfig, $request);

        // grid test
        $itemList = $grid->getItemList();

        $this->assertEquals(1 , count($itemList));

        $expected = array(
            'node.content' => 'I like it!',
            'node.user' => "toto",
            'node.parentId' => 0,
            'node.id' => 11,
            'assoc.id' => 1,
            'assoc.name' => 'test assoc',
            'intervals' => '0',
            'node.createdAt' => new \DateTime('2010-04-21 12:14:20')
        );

        $this->assertEquals($expected, $itemList[0]);

    }


    public function testGridLeftJoinWithoutGroupBy()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
//        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
//        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select("node, mn")
            ->from('Kitpages\DataGridBundle\Tests\TestEntities\Node', 'node')
            ->leftJoin('node.mainNode', 'mn')
            ->orderBy('node.id', 'ASC')
        ;

        $gridConfig = $this->initGridConfig();
        $nodeIdField = new Field("node.id");
        $gridConfig->addField($nodeIdField);
        $mainNodeIdField = new Field("mn.id");
        $gridConfig->addField($mainNodeIdField);

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(11, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 3 , count($itemList));

        $cnt = 0;
        foreach($itemList as $item) {
            $cnt ++;
            $nodeId = $grid->displayGridValue($item, $nodeIdField);
            $this->assertEquals($cnt, $nodeId);

            $mainNodeId = $grid->displayGridValue($item, $mainNodeIdField);
            if ($cnt == 1) {
                // the first node should not avec a mainNodeId, see 3 first nodes of fixtures
                $this->assertEquals(null, $mainNodeId);
            } else {
                $this->assertEquals(1, $mainNodeId);
            }
        }

        $mainNodeIdField->setNullIfNotExists(true);
        $cnt = 0;
        foreach($itemList as $item) {
            $cnt ++;
            $nodeId = $grid->displayGridValue($item, $nodeIdField);
            $this->assertEquals($cnt, $nodeId);
            $mainNodeId = $grid->displayGridValue($item, $mainNodeIdField);
            if ($cnt == 1) {
                $this->assertTrue(is_null($mainNodeId));
            } else {
                $this->assertEquals(1, $mainNodeId);
            }
        }
    }

    public function testGridFilter()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_grid_grid_filter", "foouser");
        $request->query->set("kitdg_grid_grid_sort_field", "node.createdAt");
        $request->query->set("kitdg_paginator_paginator_currentPage", 2);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        $gridConfig = $this->initGridConfig();

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(2, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 2 , count($itemList));
        $this->assertEquals( 8 , $itemList[0]["node.id"]);
        $this->assertEquals( 1 , $paginator->getCurrentPage());

        $request->query->set("kitdg_grid_grid_sort_field", "node.user");
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $itemList = $grid->getItemList();
        $this->assertEquals( 6 , $itemList[0]["node.id"]);

        $request->query->set("kitdg_grid_grid_filter", "foo");
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $itemList = $grid->getItemList();
        $this->assertEquals( 3 , count($itemList));

    }

    public function testGridUtf8Filter()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_grid_grid_filter", "foouser");
        $request->query->set("kitdg_grid_grid_sort_field", "node.createdAt");
        $request->query->set("kitdg_paginator_paginator_currentPage", 2);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        $gridConfig = $this->initGridConfig();

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(2, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 2 , count($itemList));
        $this->assertEquals( 8 , $itemList[0]["node.id"]);
        $this->assertEquals( 1 , $paginator->getCurrentPage());

        $request->query->set("kitdg_grid_grid_filter", "fös");
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $itemList = $grid->getItemList();
        $this->assertEquals( 1 , count($itemList));

    }

    public function testGridSelector()
    {
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->query->set("kitdg_grid_grid_selector_field", "node.user");
        $request->query->set("kitdg_grid_grid_selector_value", "foouser");
        $request->query->set("kitdg_grid_grid_sort_field", "node.createdAt");
        $request->query->set("kitdg_paginator_paginator_currentPage", 2);
        // create gridManager instance
        $gridManager = $this->getGridManager();

        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        $gridConfig = $this->initGridConfig();

        // get paginator
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $paginator = $grid->getPaginator();

        // tests paginator
        $this->assertEquals(2, $paginator->getTotalItemCount());

        // grid test
        $itemList = $grid->getItemList();
        $this->assertEquals( 2 , count($itemList));
        $this->assertEquals( 8 , $itemList[0]["node.id"]);
        $this->assertEquals( 1 , $paginator->getCurrentPage());

        $request->query->set("kitdg_grid_grid_sort_field", "node.user");
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $itemList = $grid->getItemList();
        $this->assertEquals( 6 , $itemList[0]["node.id"]);

        $request->query->set("kitdg_grid_grid_selector_value", "5");
        $gridConfig->setQueryBuilder($queryBuilder);
        $grid = $gridManager->getGrid($gridConfig, $request);
        $itemList = $grid->getItemList();
        $this->assertEquals( 0 , count($itemList));

    }

}
