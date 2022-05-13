<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Kitpages\DataGridBundle\Grid\Grid;
use Kitpages\DataGridBundle\Grid\Field;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Kitpages\DataGridBundle\Tests\Grid\ConversionSubscriber;
use PHPUnit\Framework\TestCase;


class GridTest extends TestCase
{
    private $grid;
    private $now;
    private $row;
    public function setUp()
    {
        $dispatcher = new EventDispatcher();

        $this->grid = new Grid();
        $this->grid->setDispatcher($dispatcher);
        $this->now = new \DateTime();
        $this->row = array(
            "node.id" => 12,
            "company.name" => "Test Company",
            'node.html' => "<a>",
            "node.createdAt" => $this->now
        );
        $this->mockField = $this->getMockBuilder('Kitpages\DataGridBundle\Grid\Field')
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
            ->will($this->onConsecutiveCalls('company.name', 'node.createdAt', 'node.id', 'node.html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals("Test Company", $displayValue);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals($this->now->format("Y-m-d H:i:s"), $displayValue);

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
            ->will($this->onConsecutiveCalls('node.html'));

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
            ->will($this->onConsecutiveCalls('company.name', 'node.html'));

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
            ->will($this->returnValue(function($value, $row) { return strtoupper($value).';'.$row["node.id"]; }));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->onConsecutiveCalls('company.name', 'node.html'));

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('TEST COMPANY;12', $displayValue);
        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('&lt;A&gt;;12', $displayValue);
    }

    public function testDisplayGridValueConvertionEvent()
    {
        $this->mockField->expects($this->any())
            ->method('getAutoEscape')
            ->will($this->returnValue(true));

        $this->mockField->expects($this->any())
            ->method('getFieldName')
            ->will($this->returnValue('company.name'));

        $subscriber = new ConversionSubscriber();
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $this->grid->setDispatcher($dispatcher);

        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('12;Test Company', $displayValue);

        $subscriber->setIsDefaultPrevented(true);
        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('company.name;preventDefault;Test Company', $displayValue);

        $subscriber->setAfterActivated(true);
        $displayValue = $this->grid->displayGridValue($this->row, $this->mockField);
        $this->assertEquals('after;company.name;preventDefault;Test Company', $displayValue);
    }
}
