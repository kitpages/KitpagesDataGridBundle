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

        $this->assertInstanceOf('Kitpages\DataGridBundle\Grid\Field', $this->gridConfig->getFieldByName($fieldName));
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
}