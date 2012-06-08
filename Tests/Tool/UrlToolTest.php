<?php
namespace Kitpages\DataGridBundle\Tests\Tool;

use Kitpages\DataGridBundle\Tool\UrlTool;

class UrlToolTest extends \PHPUnit_Framework_TestCase
{
    public function testChangeRequestQueryString()
    {
        $urlTool = new UrlTool();
        $url = "/titi?key1=val1&key2=val2";
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            "key1",
            "test"
        );
        $this->assertEquals("/titi?key1=test&key2=val2", $newUrl);
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            array(
                "key1"=>"test1",
                "key2"=>"test2"
            )
        );
        $this->assertEquals("/titi?key1=test1&key2=test2", $newUrl);
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            "key3",
            "val3"
        );
        $this->assertEquals("/titi?key1=val1&key2=val2&key3=val3", $newUrl);
    }

}
