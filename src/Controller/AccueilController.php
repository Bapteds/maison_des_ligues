<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Hotel;
use App\Entity\Vacation;
use App\Repository\HotelRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $hotels = $doctrine->getRepository(Hotel::class)->findAll();
        $ateliers = $doctrine->getRepository(Atelier::class)->findAll();

        return $this->render('accueil/index.html.twig', [
            'hotels' => $hotels,
            'ateliers' => $ateliers,
        ]);
    }
}
