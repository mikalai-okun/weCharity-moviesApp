<?php

namespace AppBundle\Controller;

use Abibockun\SimpleCurlConnector\SimpleCurlConnector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /** @var SimpleCurlConnector $curl */
    private $curl;

    /**
     * @Route("/", name="app_movies")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->setUpCurl();

        $apiRequest = 'popular';
        $currentPage = 1;
        if ($request->query->get('page')) {
            $currentPage = (integer) $request->query->get('page');
        }
        if ($request->query->get('apiRequest') === 'top_rated') {
            $apiRequest = 'top_rated';
        }

        $movies = $this->curl->send(
            '/movie/'.$apiRequest.'?api_key='.$this->getParameter('themoviedb_api_key').'&page='.$currentPage
        );

        return $this->render('@App/Default/index.html.twig', [
            'movies' => $movies,
            'currentPage' => $currentPage,
            'apiRequest' => $apiRequest
        ]);
    }

    /**
     * @Route("/{movieId}", name="app_movie_details")
     *
     * @param $movieId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction($movieId)
    {
        $this->setUpCurl();

        $movie = $this->curl->send(
            '/movie/'.$movieId.'?api_key='.$this->getParameter('themoviedb_api_key')
        );

        $trailers = $this->curl->send(
            '/movie/'.$movieId.'/videos?api_key='.$this->getParameter('themoviedb_api_key')
        );

        return $this->render('@App/Default/details.html.twig', [
            'movie' => $movie,
            'trailers' => $trailers
        ]);
    }

    /**
     * Set Up Default CURL Configuration.
     */
    private function setUpCurl()
    {
        $this->curl = $this->get('app.curl.connector');
        $this->curl->setEndPointBaseUrl($this->getParameter('themoviedb_endpoint_url'));
        $this->curl->setExtraHeaders([
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ["Accept: application/json"],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
    }
}
