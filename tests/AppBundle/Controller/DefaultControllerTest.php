<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Total Pages', $crawler->text());

        $link = $crawler
            ->filter('a.movie-link')
            ->eq(1)
            ->link()
        ;
        dump($link);
        $crawler = $client->click($link);
    }
}
