<?php
namespace Kitpages\DataGridBundle\Tests\Paginator;

use Kitpages\DataGridBundle\Paginator\PaginatorManager;
use Kitpages\DataGridBundle\Paginator\PaginatorConfig;
use Kitpages\DataGridBundle\Tests\BundleOrmTestCase;


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

    public function testPaginator()
    {
        // create queryBuilder
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Kitpages\DataGridBundle\Tests\TestEntities\Node');
        $queryBuilder = $repository->createQueryBuilder("node");
        $queryBuilder->select("node");

        // create EventDispatcher mock
        $service = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $_SERVER["REQUEST_URI"] = "/foo";

        // create gridManager instance
        $gridManager = new PaginatorManager($service);

        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.id");
        $paginatorConfig->setItemCountInPage(3);

        // get paginator
        $paginator = $gridManager->getPaginator($queryBuilder, $paginatorConfig, $request);

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
        $service = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        // create Request mock (ok this is not a mock....)
        $request = new \Symfony\Component\HttpFoundation\Request();
        $_SERVER["REQUEST_URI"] = "/foo";

        // create gridManager instance
        $gridManager = new PaginatorManager($service);

        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.user");
        $paginatorConfig->setItemCountInPage(3);

        // get paginator
        $paginator = $gridManager->getPaginator($queryBuilder, $paginatorConfig, $request);

        // tests
        $this->assertEquals(6, $paginator->getTotalItemCount());

        $this->assertEquals(2, $paginator->getTotalPageCount());

        $this->assertEquals(array(1,2), $paginator->getPageRange());

        $this->assertEquals(1 , $paginator->getCurrentPage());
        $this->assertEquals(2, $paginator->getNextButtonPage());
    }
}
