<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Kitpages\DataGridBundle\Grid\Field;
use Kitpages\DataGridBundle\Grid\GridConfig;

class GridConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GridConfig
     */
    protected $gridConfig;

    protected function setUp()
    {
        $this->gridConfig = new GridConfig();

        parent::setUp();
    }

    public function testCanAddAndRetrieveSingleFieldLegacySyntax()
    {
        $fieldName = uniqid();

        $this->gridConfig->addField(new Field($fieldName));

        $this->assertInstanceOf('Kitpages\DataGridBundle\Grid\Field', $this->gridConfig->getFieldByName($fieldName));
    }

    public function testCanAddAndRetrieveSingleFieldNewSyntax()
    {
        $fieldName = uniqid();
        $this->gridConfig->addField($fieldName, array(
            'label' => $fieldName,
        ));

        $this->assertInstanceOf(Field::class, $this->gridConfig->getFieldByName($fieldName));
        $this->assertEquals($fieldName, $this->gridConfig->getFieldByName($fieldName)->getLabel());
    }

    public function testAddFieldWrongArgumentType()
    {
        $arguments = array(true, 1, 2.2, array(), new \stdClass(), null, function () { }, );

        foreach ($arguments as $argument) {
            try {
                $this->gridConfig->addField($argument);
                $this->fail();
            } catch (\InvalidArgumentException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function testTags()
    {
        $this->gridConfig->addField(
            new Field('f1', array(), array('foo', 'bar'))
        );
        $this->gridConfig->addField(
            new Field('f2', array(), array('bar'))
        );
        $this->gridConfig->addField(
            new Field('f3', array(), array('foo', 'bar', 'biz'))
        );
        $this->assertCount(1, $this->gridConfig->getFieldListByTag('biz'));
        $this->assertCount(2, $this->gridConfig->getFieldListByTag('foo'));
        $this->assertCount(3, $this->gridConfig->getFieldListByTag('bar'));
        $this->assertCount(0, $this->gridConfig->getFieldListByTag('gloubi'));
        $field = $this->gridConfig->getFieldListByTag('biz')[0];
        $this->assertEquals('f3', $field->getFieldName());
    }
}
