<?php
namespace Kitpages\DataGridBundle\Tests\Config;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Fonctionnal
 */
class TwigExtensionTest
    extends WebTestCase
{
    public function testConfigParsing()
    {
        $client = self::createClient();
        /**
         * @var TwigEngine
         */
        $templating = $client->getContainer()->get('templating');
        $this->assertEquals(
            "@KitpagesDataGrid/Grid/grid-standard.html.twig",
            $templating->render("globals.html.twig")
        );
    }


}
