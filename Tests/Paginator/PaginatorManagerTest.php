<?php
namespace Kitpages\DataGridBundle\Tests\Paginator;

use Kitpages\DataGridBundle\Paginator\PaginatorManager;
use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use Kitpages\DataGridBundle\Tests\BundleOrmTestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;


class PaginatorManagerTest extends BundleOrmTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    protected function getPaginatorManager()
    {
        // create EventDispatcher mock
        $service = new EventDispatcher();
        $parameters = array(
            "default_twig" => "toto.html.twig",
            "item_count_per_page" => 50,
            "visible_page_count_in_paginator" => 5
        );
        // create paginatorManager
        $paginatorManager = new PaginatorManager($service, $parameters);
        return $paginatorManager;

    }
    public function testPaginator()
    {
        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $_SERVER["REQUEST_URI"] = "/foo";
        $paginatorManager = $this->getPaginatorManager();

        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.id");
        $paginatorConfig->setItemCountInPage(3);

        // get paginator
        $paginatorConfig->setQueryBuilder($queryBuilder);
        $paginator = $paginatorManager->getPaginator($paginatorConfig, $request);

        // tests
        $this->assertEquals(11, $paginator->getTotalItemCount());

        $this->assertEquals(4, $paginator->getTotalPageCount());

        $this->assertEquals(array(1,2,3,4), $paginator->getPageRange());

        $this->assertEquals(1 , $paginator->getCurrentPage());
        $this->assertEquals(2, $paginator->getNextButtonPage());
    }

    public function testPaginatorGroupBy()
    {
        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node.user, count(node.id) as cnt");
        $queryBuilder->groupBy("node.user");

        // create EventDispatcher mock
        $service = new EventDispatcher();
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $_SERVER["REQUEST_URI"] = "/foo";

        // create gridManager instance
        $paginatorManager = $this->getPaginatorManager();


        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.user");
        $paginatorConfig->setItemCountInPage(3);

        // get paginator
        $paginatorConfig->setQueryBuilder($queryBuilder);
        $paginator = $paginatorManager->getPaginator($paginatorConfig, $request);

        // tests
        $this->assertEquals(6, $paginator->getTotalItemCount());

        $this->assertEquals(2, $paginator->getTotalPageCount());

        $this->assertEquals(array(1,2), $paginator->getPageRange());

        $this->assertEquals(1 , $paginator->getCurrentPage());
        $this->assertEquals(2, $paginator->getNextButtonPage());
    }
}
