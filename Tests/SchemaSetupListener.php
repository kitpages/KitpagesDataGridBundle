<?php
namespace Kitpages\DataGridBundle\Tests;

use DoctrineExtensions\PHPUnit\Event\EntityManagerEventArgs;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;

class SchemaSetupListener
{
    public function preTestSetUp(EntityManagerEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        $schemaTool = new SchemaTool($em);

        $cmf = $em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }
}
