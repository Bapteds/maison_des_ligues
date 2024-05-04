<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Inscription;
use App\Entity\Nuite;
use App\Entity\Proposer;
use App\Entity\Restauration;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InscriptionController extends AbstractController
{

    private $api_url;
    private $manager;
    private $client;
    private $doctrine;
    private $mailer;

    public function __construct(ManagerRegistry $manager, EntityManagerInterface $doctrine, HttpClientInterface $client, Mailer $mailer)
    {
        $this->manager = $manager;
        $this->api_url = $_ENV['API_URL'];
        $this->doctrine = $doctrine;
        $this->client = $client;
        $this->mailer = $mailer;
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $params = $request->request->all();
            if (!empty($params)) {
                $params = $this->sortParams($params);
                $params = $this->retriveValueFromIds($params);
                if ($this->doVerifDonnees($params)) {
                    $this->saveInscription($params);
                    $link = $this->generateUrl('app_validation', ['id_user' => $user->getId(), 'id_inscription' => $user->getInscription()->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
                    $this->mailer->sendEmail('maisondesligues@gouv.fr', $user->getEmail(), 'Votre demande est en attente.', $link);
                } else {
                    $this->addFlash('error', 'Formulaire non valide, veuillez recommencer.');
                    return $this->redirectToRoute('app_inscription');
                }
            }
            return $this->redirectToRoute('app_inscription');
            //$this->createInscription($params,$managerInt);


        } else {
            // Partie Affichage pour la vue
            $qualite = $this->qualiteApi($user->getIdqualite());
            $ateliers = $this->manager->getRepository(Atelier::class)->findAll(); // Je veux que ceux qui ont de la place !!
            $nuite = $this->manager->getRepository(Nuite::class)->findBy([], ['hotel' => 'ASC']);
            return $this->render('inscription/index.html.twig', ['user' => $user, 'qualite' => $qualite, 'ateliers' => $ateliers, 'nuite' => $nuite]);
        }
    }

    #[Route('/validation', name: 'app_validation')]
    public function validation(Request $request): Response
    {
        if ($request->isMethod('POST')) {
        } else {
            $user = $this->getUser();

            $idUser = $request->get('id_user');
            $idInscription = $request->get('id_inscription');
            if ($idUser != $user->getId()) {
                return $this->redirectToRoute('app_accueil');
            }
            if ($user->getInscription()->getId() != $idInscription) {
                return $this->redirectToRoute('app_accueil');
            }
            $qualite = $this->qualiteApi($user->getIdqualite());
            $ateliers = $this->manager->getRepository(Atelier::class)->findAll(); // Je veux que ceux qui ont de la place !!
            $nuite = $this->manager->getRepository(Nuite::class)->findBy([], ['hotel' => 'ASC']);
            $inscription = $this->manager->getRepository(Inscription::class)->findOneBy(['user' => $idUser, 'id' => $idInscription]);
            $montant = $this->calculerMontant($inscription);
            return $this->render('inscription/validation.html.twig', ['user' => $user, 'qualite' => $qualite, 'inscription' => $inscription, 'montant' => $montant]);
        }
    }


    /**
     * Permet de retourner le montant total
     *
     * @param array $params
     * @return void
     */
    private function calculerMontant(Inscription $inscription)
    {
        $montant = 130;
        if (count($inscription->getRestaurations()) > 0) {
            $montant += count($inscription->getRestaurations()) * 38;
        }
        if (count($inscription->getNuites())) {
            foreach ($inscription->getNuites() as $nuite) {
                if ($nuite->getHotel()->getId() == 1) {
                    if ($nuite->getCategorieChambre()->getId() == 1) {
                        $montant += 95;
                    }
                    if ($nuite->getCategorieChambre()->getId() == 2) {
                        $montant += 105;
                    }
                }
                if ($nuite->getHotel()->getId() == 2) {
                    if ($nuite->getCategorieChambre()->getId() == 1) {
                        $montant += 70;

                    }
                    if ($nuite->getCategorieChambre()->getId() == 2) {
                        $montant += 80;
                    }
                }
            }
        }return $montant;
    }



    /**
     * Permet de créer et d'enregistrer l'inscription
     *
     * @param array $params
     * @return void
     */
    private function saveInscription(array $params)
    {
        $inscription = new Inscription();
        if (isset($params['atelier'])) {
            foreach ($params['atelier'] as $atelier) {
                $inscription->setAtelier($atelier);
            }
        }
        if (isset($params['nuite'])) {
            foreach ($params['nuite'] as $nuite) {
                $inscription->setNuite($nuite);
            }
        }
        if (isset($params['acc'])) {
            foreach ($params['acc'] as $acc) {
                $inscription->setRestauration($acc);
            }
        }
        $inscription->setUser($this->getUser());
        $inscription->setEstPaye(false);
        $inscription->setDateInscription(new \DateTime('now'));
        $this->doctrine->persist($inscription);
        $this->doctrine->flush();
    }


    /**
     * Permet de récupérer toutes les valeurs sous forme d'entity pour l'inscription
     *
     * @param array $params
     * @return array
     */
    private function retriveValueFromIds(array $params): array
    {
        //AJOUTER SI NULL
        if (!empty($params['atelier'])) {
            $params['atelier'] = $this->getAtelier($params['atelier']);
        }
        if (!empty($params['nuite'])) {
            $params['nuite'] = $this->getNuite($params['nuite']);
        }
        if (!empty($params['acc'])) {
            $params['acc'] = $this->getRestauration($params['acc']);
        }

        return $params;
    }

    /**
     * Permet de vérifier les données
     *
     * @param array $params
     * @return bool
     */
    private function doVerifDonnees(array $params): bool
    {
        if (isset($params['atelier'])) {
            if (count($params['atelier']) > 5) {
                return false;
            }
            if (!empty($params['atelier'])) {
                $atelierDispo = $this->getAteliersDispo();
                foreach ($params['atelier'] as $atelier) {
                    if ($this->checkIfInArray($atelierDispo, $atelier->getId()) == false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Permet de savoir si un élément existe dans un array
     *
     * @param array $values
     * @param mixed $tocompare
     * @return void
     */
    private function checkIfInArray(array $values, mixed $tocompare)
    {
        foreach ($values as $value) {
            if ($value->getId() == $tocompare) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retourne les ateliers dans lesquels il reste des places.
     * @return array
     */
    private function getAteliersDispo(): array
    {
        $ateliers = $this->manager->getRepository(Atelier::class)->findAll();
        $ateliersDispo = [];

        foreach ($ateliers as $atelier) {
            $nbPlacesMaxi = $atelier->getNbPlacesMaxi();

            $nbInscriptions = 0;

            foreach ($atelier->getInscriptions() as $ins) {
                $nbInscriptions += 1;
            }

            if ($nbInscriptions < $nbPlacesMaxi) {
                $ateliersDispo[] = $atelier;
            }
        }
        return $ateliersDispo;
    }

    /**
     * Permet de récupérer tous les ateliers en grace aux id dans un array
     *
     * @param array $params
     * @return void
     */
    private function getAtelier(array $params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = $this->manager->getRepository(Atelier::class)->find($value);
        }
        return $params;
    }

    /**
     * Permet de récupérer toutes les restauration en grace aux id dans un array
     *
     * @param array $params
     * @return void
     */
    private function getRestauration(array $params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = $this->manager->getRepository(Restauration::class)->find($value);
        }
        return $params;
    }

    /**
     * Permet de récupérer toutes les nuitées en grace aux id dans un array
     *
     * @param array $params
     * @return void
     */
    private function getNuite(array $params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = $this->manager->getRepository(Nuite::class)->find($value);
        }
        return $params;
    }

    /**
     * Permet de trier le tableau avec tous les éléménts de mon formulaire
     *
     * @param array $params
     * @return array
     */
    private function sortParams(array $params): array
    {
        $sorted = array();
        foreach ($params as $key => $value) {
            if (strtok($key, '-') == 'atelier') { // Si la key == atelier
                if (isset($sorted['atelier'])) { // Si le tab avec atelier existe 
                    array_push($sorted['atelier'], $value); // J'ajoute la value (id) 
                } else {
                    $sorted['atelier'] = [$value]; // Sinon je créer ce tab
                }
            }
            if (strtok($key, '-') == 'nuite') {
                if (isset($sorted['nuite'])) {
                    array_push($sorted['nuite'], $value);
                } else {
                    $sorted['nuite'] = [$value];
                }
            }
            if (strtok($key, '-') == 'acc') {
                if (isset($sorted['acc'])) {
                    array_push($sorted['acc'], $value);
                } else {
                    $sorted['acc'] = [$value];
                }
            }
        }
        return $sorted;
    }

    /**
     * Fonction qui fait appel à l'API, pour récupérer un club
     *
     * @param string $numlicence
     * @param HttpClientInterface $client
     * @return array
     */
    private function qualiteApi(string $numqualite): array
    {
        $response = $this->client->request(
            'GET',
            $this->api_url . '/api/qualites/' . $numqualite
        );
        $json = json_decode($response->getContent(), true);
        return $json != null ? $json : [];
    }
}
