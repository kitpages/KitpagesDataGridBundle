<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Kitpages\DataGridBundle\Grid\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $field = new Field("id");
        $this->assertEquals($field->getFieldName(), "id");

        $field = new Field("phone", array(
            "label" => "Phone",
            "sortable" => true,
            "filterable" => true,
            "visible" => false,
            "formatValueCallback" => function($value) { return strtoupper($value);},
            "autoEscape" => true,
            "translatable" => true,
            'category' => "my.category",
            'nullIfNotExists' => true
        ));
        $this->assertEquals("Phone", $field->getLabel());
        $this->assertTrue($field->getSortable());
        $this->assertTrue($field->getFilterable());
        $this->assertFalse($field->getVisible());
        $this->assertNotNull($field->getFormatValueCallback());
        $this->assertTrue($field->getFilterable());
        $this->assertEquals("my.category", $field->getCategory());
        $this->assertTrue( $field->getNullIfNotExists());

        $field = new Field("test");
        $this->assertFalse( $field->getNullIfNotExists());
    }

    public function testWrongParameterConstructor()
    {
        try {
            $field = new Field("phone", array(
                "foo" => "bar"
            ));
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    public function testTagSystem()
    {
        $field = new Field(
            'phone',
            array(
                'label' => 'Phone',
                'sortable' => true,
                'filterable' => true,
                'visible' => false,
                'formatValueCallback' => function($value) { return strtoupper($value);},
                'autoEscape' => true,
                'translatable' => true,
                'category' => 'my.category',
                'nullIfNotExists' => true
            ),
            array (
                'foo',
                'bar'
            )
        );
        $this->assertTrue($field->hasTag('foo'));
        $this->assertFalse($field->hasTag('tutu'));
        $this->assertEquals(array('foo', 'bar'), $field->getTagList());
    }
}
