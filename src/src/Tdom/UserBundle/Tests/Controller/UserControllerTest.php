<?php

namespace Tdom\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/registration');
    }

    public function testProfile()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/profile/{id}');
    }

}
