<?php

namespace Kitpages\DataGridBundle\Tests;

use Kitpages\DataGridBundle\Tests\SchemaSetupListener;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;

use DoctrineExtensions\PHPUnit\OrmTestCase;

class BundleOrmTestCase
    extends OrmTestCase
{
    protected function createEntityManager()
    {
        // event manager used to create schema before tests
        $eventManager = new EventManager();
        $eventManager->addEventListener(array("preTestSetUp"), new SchemaSetupListener());

        // doctrine xml configs and namespaces
        $configPathList = array();
        if (is_dir(__DIR__.'/../Resources/config/doctrine')) {
            $dir = __DIR__.'/../Resources/config/doctrine';
            $configPathList[] = $dir;
            $prefixList[$dir] = 'Kitpages\DataGridBundle\Entities';
        }
        if (is_dir(__DIR__.'/_doctrine/config')) {
            $dir = __DIR__.'/_doctrine/config';
            $configPathList[] = $dir;
            $prefixList[$dir] = 'Kitpages\DataGridBundle\Tests\TestEntities';
        }
        // create drivers (that reads xml configs)
        $driver = new \Symfony\Bridge\Doctrine\Mapping\Driver\XmlDriver($configPathList);
        $driver->setNamespacePrefixes($prefixList);

        // create config object
        $config = new Configuration();
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setMetadataDriverImpl($driver);
        $config->setProxyDir(__DIR__.'/TestProxies');
        $config->setProxyNamespace('Kitpages\DataGridBundle\Tests\TestProxies');
        $config->setAutoGenerateProxyClasses(true);
        //$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

        // create entity manager
        $em = EntityManager::create(
            array(
                'driver' => 'pdo_sqlite',
                'memory' => true
            ),
            $config,
            $eventManager
        );

        return $em;
    }

    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(__DIR__."/_doctrine/dataset/entityFixture.xml");
    }

}
