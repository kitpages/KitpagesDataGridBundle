<?php
namespace Kitpages\DataGridBundle\Tests\Model;

use Kitpages\DataGridBundle\Model\Field;
use Kitpages\DataGridBundle\Model\PaginatorConfig;
use Kitpages\DataGridBundle\Service\GridManager;
use Kitpages\DataGridBundle\Tests\BundleOrmTestCase;


class GridManagerTest extends BundleOrmTestCase
{
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

        // create gridManager instance
        $gridManager = new GridManager($service);

        // configure paginator
        $paginatorConfig = new PaginatorConfig();
        $paginatorConfig->setCountFieldName("node.id");

        // get paginator
        $paginator = $gridManager->getPaginator($queryBuilder, $paginatorConfig, $request);

        // tests
        $this->assertEquals($paginator->getTotalItemCount(), 2);
    }
}
