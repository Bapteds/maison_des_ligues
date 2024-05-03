<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Vacation;
use App\Repository\AtelierRepository;
use App\Repository\ThemeRepository;
use App\Repository\VacationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{


    /**
     * Retourne la page qui permet de choisir le type d'élément à ajouter
     *
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/admin/edit', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $ateliers = $doctrine->getRepository(Atelier::class)->findAll();

        return $this->render('admin/crud/index.html.twig', ['ateliers' => $ateliers]);
    }

    /**
     * Retourne la page qui permet d'ajouter un atelier
     * @param Request $request
     * @param AtelierRepository $repo
     * @return void
     */
    #[Route('/admin/ajout-atelier', name: 'add_atelier')]
    public function add_atelier(Request $request, AtelierRepository $repo)
    {
        if ($request->isMethod('POST')) {
            $libelle = $request->get('libelle');
            $nb_place = $request->get('nb_place');
            if ($libelle && $nb_place) {
                $repo->save($request->get('nb_place'), $request->get('libelle'));
                $this->addFlash('success', 'Atelier crée');
                return $this->redirectToRoute('app_admin');
            } else {
                $this->addFlash('error', 'Les champs sont vides');
                return $this->redirectToRoute('app_admin');
            }
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    /**
     * Retourne la page qui permet d'ajouter un thème
     *
     * @param Request $request
     * @param ThemeRepository $repo
     * @param ManagerRegistry $doctrine
     * @return void
     */
    #[Route('/admin/ajout-theme', name: 'add_theme')]
    public function add_theme(Request $request, ThemeRepository $repo, ManagerRegistry $doctrine)
    {
        if ($request->isMethod('POST')) {
            $libelle = $request->get('libelle_theme');
            $idAtelier = $request->get('id-atelier');
            $atelier = $doctrine->getRepository(Atelier::class)->find($idAtelier);
            if (!$atelier) {
                $this->addFlash('error', 'L\'atelier n\'existe pas');
                return $this->redirectToRoute('app_admin');
            } elseif (!$libelle) {
                $this->addFlash('error', 'Veuillez saisir un libelle');
                return $this->redirectToRoute('app_admin');
            } else {
                $repo->save($libelle, $atelier);
                return $this->redirectToRoute('app_admin');
            }
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    /**
     * Retourne la page qui permet d'ajouter une vacation
     *
     * @param Request $request
     * @param VacationRepository $repo
     * @param ManagerRegistry $doctrine
     * @return void
     */
    #[Route('/admin/ajout-vacation', name: 'add_vacation')]
    public function add_vacation(Request $request, VacationRepository $repo, ManagerRegistry $doctrine)
    {
        if ($request->isMethod('POST')) {
            $atelier = $doctrine->getRepository(Atelier::class)->find($request->get('id-atelier'));
            if ($request->get('heure_debut') && $request->get('date_debut') && $request->get('heure_fin') && $request->get('date_fin')) {
                $date_debut = $request->get('date_debut') . " " . $request->get('heure_debut') . ":00";
                $date_fin = $request->get('date_fin') . " " . $request->get('heure_fin') . ":00";
                if ($date_debut > $date_fin || $date_debut == $date_fin) {
                    $this->addFlash('error', 'La date de fin doit être supérieure à celle du début');
                    return $this->redirectToRoute('app_admin');
                }
                if (!$atelier) {
                    $this->addFlash('error', 'L\'atelier n\'existe pas');
                    return $this->redirectToRoute('app_admin');
                } else {
                    $repo->save($date_debut, $date_fin, $atelier);
                    return $this->redirectToRoute('app_admin');
                }
            } else {
                $this->addFlash('error', 'Veuillez saisir la date');
                return $this->redirectToRoute('app_admin');
            }
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    /**
     * Retourne la page qui permet de modifier une vacation
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return void
     */
    #[Route('/admin/edit-vacation', name: 'select_atelier')]
    public function selectAtelier(Request $request, ManagerRegistry $doctrine)
    {
        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('select_vacation', ['idatelier' => $request->get('id-atelier')]);
        }
        $ateliers = $doctrine->getRepository(Atelier::class)->findAll();
        return $this->render('admin/crud/edit.html.twig', ['ateliers' => $ateliers, 'vacations' => null, 'vacation' => null]);
    }

    /**
     * Retourne la page qui permet de choisir une vacation
     */
    #[Route('/admin/edit-vacation/{idatelier}/vacation', name: 'select_vacation')]
    public function selectVacation(int $idatelier, Request $request, ManagerRegistry $doctrine)
    {

        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('edit_vacation', ['idatelier' => $idatelier, 'idvacation' => $request->get('id-vacation')]);
        }

        $ateliers = $doctrine->getRepository(Atelier::class)->find($idatelier);
        $vacations = $ateliers->getVacations();
        return $this->render('admin/crud/edit.html.twig', ['ateliers' => null, 'vacations' => $vacations, 'vacation' => null]);
    }

    /**
     * Permet de retourner la page de modification d'une vacation, mais aussi de le mettre à jour
     */
    #[Route('/admin/edit-vacation/{idatelier}/vacation/{idvacation}', name: 'edit_vacation')]
    public function editVacation(int $idatelier, int $idvacation, Request $request, ManagerRegistry $doctrine)
    {
        $vacation = $doctrine->getRepository(Vacation::class)->find($idvacation);
        if ($request->isMethod('POST')) {
            if ($request->get('heure_debut') && $request->get('date_debut') && $request->get('heure_fin') && $request->get('date_fin')) {
                $date_debut = $request->get('date_debut') . " " . $request->get('heure_debut') . ":00";
                $date_fin = $request->get('date_fin') . " " . $request->get('heure_fin') . ":00";
                if ($date_debut > $date_fin || $date_debut == $date_fin) {
                    $this->addFlash('error', 'La date de fin doit être supérieure à celle du début');
                    return $this->redirectToRoute('edit_vacation', ['idvacation' => $idvacation, 'idatelier' => $idatelier]);
                } else {
                    $vacation->setDateHeureDebut($date_debut);
                    $vacation->setDateHeureFin($date_fin);
                    $doctrine->getManager()->flush();
                    $this->addFlash('success', 'Vacation mise à jour');
                }
            }
        }
        return $this->render('admin/crud/edit.html.twig', ['ateliers' => null, 'vacations' => null, 'vacation' => $vacation]);
    }
}
