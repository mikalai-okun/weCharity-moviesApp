<?php

namespace Tests\AppBundle\Controller;

use Abibockun\SimpleCurlConnector\SimpleCurlConnector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testApiConnection()
    {
        $client = static::createClient();

        $curl = new SimpleCurlConnector();
        $curl->setEndPointBaseUrl($client->getKernel()->getContainer()->getParameter('themoviedb_endpoint_url'));
        $curl->setExtraHeaders([
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ["Accept: application/json"],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $movies = $curl->send(
            '/movie/popular?api_key='.$client->getKernel()->getContainer()->getParameter('themoviedb_api_key')
        );

        $this->assertInternalType('array',$movies->results);
        $this->assertNotEmpty($movies->results);
    }

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
        $crawler = $client->click($link);
        $this->assertContains('Mark as favorite', $crawler->text());
    }
}
