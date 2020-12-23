<?php
namespace Kitpages\DataGridBundle\Tests\Config;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ConfigTest
    extends WebTestCase
{
    public function testConfigParsing()
    {
        $client = self::createClient();
        $gridParameters = $client->getContainer()->getParameter('kitpages_data_grid.grid');
        $this->assertEquals("@KitpagesDataGrid/Grid/grid-standard.html.twig", $gridParameters["default_twig"]);
        $this->assertEquals('\Kitpages\DataGridBundle\Hydrators\DataGridHydrator', $gridParameters["hydrator_class"]);
    }


}
