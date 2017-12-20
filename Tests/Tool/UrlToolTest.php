<?php
namespace Kitpages\DataGridBundle\Tests\Tool;

use Kitpages\DataGridBundle\Tool\UrlTool;
use PHPUnit\Framework\TestCase;

class UrlToolTest extends TestCase
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
    public function testChangeRequestUtf8QueryString()
    {
        $urlTool = new UrlTool();
        $url = "/titi?key1=val1&key2=val2";
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            "key1",
            "fös"
        );
        $this->assertEquals("/titi?key1=f%C3%B6s&key2=val2", $newUrl);
        $newUrl = $urlTool->changeRequestQueryString(
            $newUrl,
            "key3",
            "mystring=-+ glou"
        );
        $this->assertEquals("/titi?key1=f%C3%B6s&key2=val2&key3=mystring%3D-%2B%20glou", $newUrl);
    }
    public function testChangeRequestWithArray()
    {
        $urlTool = new UrlTool();
        $url = "/titi?tab[]=val_tab1&tab[]=val_tab2&key2=val2";
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            "key2",
            "glou"
        );
        $this->assertEquals("/titi?tab[]=val_tab1&tab[]=val_tab2&key2=glou", $newUrl);
        $newUrl = $urlTool->changeRequestQueryString(
            $url,
            "tab",
            array("newval1", "newval2", "newval3")
        );
        $this->assertEquals("/titi?tab[]=newval1&tab[]=newval2&tab[]=newval3&key2=val2", $newUrl);
    }
}
