<?php
namespace Kitpages\DataGridBundle\Tests\Model;

use Kitpages\DataGridBundle\Model\Grid;
use Kitpages\DataGridBundle\Model\Field;

class GridTest extends \PHPUnit_Framework_TestCase
{
    private $grid;
    private $now;
    private $row;
    public function setUp()
    {
        $this->grid = new Grid();
        $this->grid->setRootAliases(array("shopOrder"));
        $this->now = new \DateTime();
        $this->row = array(
            "id" => 12,
            "company" => array(
                "name" => "Test Company"
            ),
            'html' => "<a>",
            "createdAt" => $this->now
        );
        $this->mockField = $this->getMockBuilder('Kitpages\DataGridBundle\Model\Field')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructor()
    {
        $grid = new Grid();
        $this->assertTrue($grid instanceof Grid);
    }

    public function testDisplayGridValue()
    {
        $this->mockField->expects($this->any())
            ->method('getAutoEscape')
            ->will($this->returnValue(true));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->onConsecutiveCalls('company.name', 'shopOrder.company.name', 'shopOrder.createdAt', 'shopOrder.id', 'id', 'shopOrder.html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals("Test Company", $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals("Test Company", $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals($this->now->format("Y-m-d H:i:s"), $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals(12, $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals(12, $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('&lt;a&gt;', $displayValue);

    }

    public function testDisplayGridValueAutoEscapeFalse()
    {
        $this->mockField->expects($this->any())
            ->method('getAutoEscape')
            ->will($this->returnValue(false));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->onConsecutiveCalls('shopOrder.html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('<a>', $displayValue);
    }

    public function testDisplayGridValueCallbackSimple()
    {
        $this->mockField->expects($this->any())
            ->method('getAutoEscape')
            ->will($this->returnValue(false));
        $this->mockField->expects($this->any())
            ->method('getFormatValueCallback')
            ->will($this->returnValue(function($value){ return strtoupper($value); }));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->onConsecutiveCalls('company.name', 'html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('TEST COMPANY', $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('<A>', $displayValue);
    }

    public function testDisplayGridValueCallbackExtended()
    {
        $this->mockField->expects($this->any())
            ->method('getAutoEscape')
            ->will($this->returnValue(true));
        $this->mockField->expects($this->any())
            ->method('getFormatValueCallback')
            ->will($this->returnValue(function($value, $row) { return strtoupper($value).';'.$row["id"]; }));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->onConsecutiveCalls('company.name', 'html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('TEST COMPANY;12', $displayValue);
        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('&lt;A&gt;;12', $displayValue);
    }
}
