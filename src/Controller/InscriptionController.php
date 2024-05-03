<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InscriptionController extends AbstractController
{

    private $api_url;

    private function __construct()
    {
        $this->api_url = $_ENV['API_URL'];
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function index(HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        $club = $this->clubApi($user->get);
        return $this->render('inscription/index.html.twig',['user' => $user]);
    }


    /**
     * Fonction qui fait appel à l'API, pour récupérer un club
     *
     * @param string $numlicence
     * @param HttpClientInterface $client
     * @return array
     */
    private function clubApi(string $numclub, HttpClientInterface $client): array
    {
        $response = $client->request(
            'GET',
            $this->api_url . '/api/clubs/' . $numclub
        );
        $json = json_decode($response->getContent(), true);
        if (!isset($json['hydra:member'][0])) {
            return [];
        }
        return $json['hydra:member'][0];
    }

}
