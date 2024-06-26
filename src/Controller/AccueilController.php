<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Hotel;
use App\Entity\Theme;
use App\Entity\Vacation;
use App\Repository\HotelRepository;
use App\Services\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

/**
 * Retourne la vue de la page d'acceuil
 *
 * @param ManagerRegistry $doctrine
 * @param Mailer $mailer
 * @return Response
 */
    #[Route('/', name: 'app_accueil')]
    public function index(ManagerRegistry $doctrine, Mailer $mailer): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $hotels = $doctrine->getRepository(Hotel::class)->findAll();
        $ateliers = $doctrine->getRepository(Atelier::class)->findAll();

        return $this->render('accueil/index.html.twig', [
            'hotels' => $hotels,
            'ateliers' => $ateliers,
        ]);
    }
}
