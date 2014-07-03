<?php

namespace Tdom\GameBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testMygame()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/mygames');
    }

}
