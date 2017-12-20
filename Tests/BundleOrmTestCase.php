<?php
namespace Kitpages\DataGridBundle\Tests;

use Doctrine\ORM\EntityManager;
use Kitpages\DataGridBundle\Tests\TestEntities\NodeAssoc;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Kitpages\DataGridBundle\Tests\TestEntities\Node;

class BundleOrmTestCase
    extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var
     */
    protected $pdo;
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function createEntityManager()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->run(new ArrayInput(array(
            'doctrine:schema:drop',
            '--force' => true
        )));
        $application->run(new ArrayInput(array(
            'doctrine:schema:create'
        )));
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->createTestEntities();
        return $this->em;
    }

    public function getConnection()
    {
        $pdo = $this->em->getConnection();
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }

    public function getEntityManager()
    {
        return $this->createEntityManager();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createTestEntities()
    {
        //    <node id="1" content="foobar" user="joe" created_at="2010-04-24 17:15:23" parent_id=""/>
/*
 *     <node_assoc id="1" name="test assoc"/>

    <node id="1" content="foobar" user="joe" created_at="2010-04-24 17:15:23" parent_id=""/>
    <node id="2" content="I like it!" user="toto" created_at="2010-04-26 12:14:20" parent_id="1"  mainNodeId="1"/>
    <node id="3" content="I like it!" user="toto" created_at="2010-04-27 12:14:20" parent_id="1"  mainNodeId="1"/>
    <node id="4" content="Hello bob" user="toto" created_at="2010-04-28 12:14:20" parent_id="2"/>
    <node id="5" content="I like it!" user="toto" created_at="2010-04-29 12:14:20" parent_id=""/>
    <node id="6" content="Hello robert" user="foouser" created_at="2010-04-26 12:14:20" parent_id=""/>
    <node id="7" content="I like it!" user="fös" created_at="2010-04-17 12:14:20" parent_id=""/>
    <node id="8" content="Hello foo" user="foouser" created_at="2010-04-18 12:14:20" parent_id=""/>
    <node id="9" content="I fös it!" user="foo" created_at="2010-04-19 12:14:20" parent_id=""/>
    <node id="10" content="I like it!" user="bar" created_at="2010-04-20 12:14:20" parent_id="" mainNodeId="1"/>
    <node id="11" assoc_id="1" content="I like it!" user="toto" created_at="2010-04-21 12:14:20" parent_id="" mainNodeId="1"/>

 */
        $this->em->persist($nodeAssoc = (new NodeAssoc())
            ->setId(1)
            ->setName('test assoc')
        );

        $node1 = (new Node())
            ->setId(1)
            ->setContent('foobar')
            ->setUser('joe')
            ->setCreatedAt(new \DateTime('2010-04-24 17:15:23'))
            ->setParentId('')
        ;
        $this->em->persist($node1);

        $node2 = (new Node())
            ->setId(2)
            ->setContent('I like it!')
            ->setUser('toto')
            ->setCreatedAt(new \DateTime('2010-04-26 12:14:20'))
            ->setParentId('1')
            ->setMainNode($node1)
        ;
        $this->em->persist($node2);

        $node3 = (new Node())
            ->setId(3)
            ->setContent('I like it!')
            ->setUser('toto')
            ->setCreatedAt(new \DateTime('2010-04-27 12:14:20'))
            ->setParentId('1')
            ->setMainNode($node1)
        ;
        $this->em->persist($node3);

        $node4 = (new Node())
            ->setId(4)
            ->setContent('Hello bob')
            ->setUser('toto')
            ->setCreatedAt(new \DateTime('2010-04-28 12:14:20'))
            ->setParentId('2')
        ;
        $this->em->persist($node4);

        $node5 = (new Node())
            ->setId(5)
            ->setContent('I like it!')
            ->setUser('toto')
            ->setCreatedAt(new \DateTime('2010-04-29 12:14:20'))
        ;
        $this->em->persist($node5);

        $node6 = (new Node())
            ->setId(6)
            ->setContent('Hello robert')
            ->setUser('foouser')
            ->setCreatedAt(new \DateTime('2010-04-26 12:14:20'))
        ;
        $this->em->persist($node6);

        $node7 = (new Node())
            ->setId(7)
            ->setContent('I like it!')
            ->setUser('fös')
            ->setCreatedAt(new \DateTime('2010-04-17 12:14:20'))
        ;
        $this->em->persist($node7);

        $this->em->persist($node8 = (new Node())
            ->setId(8)
            ->setContent('Hello foo')
            ->setUser('foouser')
            ->setCreatedAt(new \DateTime('2010-04-18 12:14:20'))
        );

        $this->em->persist($node9 = (new Node())
            ->setId(9)
            ->setContent('I fös it!')
            ->setUser('foo')
            ->setCreatedAt(new \DateTime('2010-04-19 12:14:20'))
        );

        $this->em->persist($node10 = (new Node())
            ->setId(10)
            ->setContent('I like it!')
            ->setUser('bar')
            ->setCreatedAt(new \DateTime('2010-04-20 12:14:20'))
        );

        $this->em->persist($node11 = (new Node())
            ->setId(11)
            ->setContent('I like it!')
            ->setUser('toto')
            ->setCreatedAt(new \DateTime('2010-04-21 12:14:20'))
            ->setMainNode($node1)
            ->setAssoc($nodeAssoc)
        );

        //        <node id="7" content="I like it!" user="fös" created_at="2010-04-17 12:14:20" parent_id=""/>
//    <node id="8" content="Hello foo" user="foouser" created_at="2010-04-18 12:14:20" parent_id=""/>
//    <node id="9" content="I fös it!" user="foo" created_at="2010-04-19 12:14:20" parent_id=""/>
//    <node id="10" content="I like it!" user="bar" created_at="2010-04-20 12:14:20" parent_id="" mainNodeId="1"/>
//    <node id="11" assoc_id="1" content="I like it!" user="toto" created_at="2010-04-21 12:14:20" parent_id="" mainNodeId="1"/>

        $this->em->flush();

    }

}
