<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityController extends AbstractController
{


    public function __construct() {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if (isset($error) && $error->getCode() == 0) {
            $error = 'Mot de passe, ou licence incorrect.';
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('admin/auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, UserRepository $repo, EntityManagerInterface $manager, HttpClientInterface $client): Response
    {
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            if ($password == $request->request->get('confirmation')) {
                $numlicence = $request->request->get('licence_code');
                if ($this->getUserByLicence($numlicence, $repo)) {
                    return $this->render('admin/auth/register.html.twig', ['error' => 'Licence déjà utilisée']);
                } else {
                    $licencie = $this->existLicence($numlicence, $manager, $client);
                    if (!$licencie) {
                        return $this->render('admin/auth/register.html.twig', ['error' => 'La licence n\'éxiste pas']);
                    } else {
                        $this->createUser($licencie, $password, $manager);
                        // Envoyer mail à l'user;
                    }
                    return $this->render('admin/auth/register.html.twig', ['error' => 'Demande faite, vérifiez vos mails']);
                }
            } else {
                return $this->render('admin/auth/register.html.twig', ['error' => 'Les mots de passe ne correspondent pas']);
            }
        } else {
            return $this->render('admin/auth/register.html.twig', ['error' => null]);
        }
    }

    private function getUserByLicence($numlicence, UserRepository $userRepo)
    {
        $user = $userRepo->findOneBy(['numlicence' => $numlicence]);
        return $user;
    }

    /**
     * Permet de retourner les infos du licencier si la licence existe ### API A AJOUTER ### EntityManager a dégager
     *
     * @param String $numlicence
     * @return Array
     */
    private function existLicence(string $numlicence, EntityManager $manager, HttpClientInterface $client): array
    {
        $this->licenceAPI($numlicence, $client);
        return [];
    }

    private function licenceAPI(string $numlicence, HttpClientInterface $client)
    {
        $response = $client->request(
            'GET', 'http://localhost:8888/api/licencies?page=1&numlicence='.$numlicence
        );

        dd($response->getContent());
    }


    private function licenceBDD(string $numlicence, EntityManager $manager)
    {
        $req = 'SELECT * FROM LICENCIE WHERE numlicence =:numlicence';
        $prepare = $manager->getConnection()->prepare($req);
        $result = $prepare->executeQuery(['numlicence' => $numlicence]);
        return $result->fetchAllAssociative();
    }

    /**
     * Permet de créer un nouvel user. Penser a changer role dans entity
     *
     * @param array $licencie
     * @return void
     */
    private function createUser(array $licencie, string $password, EntityManager $manager)
    {
        $user = new User();
        $user->setEmail($licencie[0]['MAIL']);
        $user->setRoles('ROLE_INSCRIT');
        $user->setPassword($password);
        $user->setNumlicence($licencie[0]['numlicence']);
        $user->setIsVerified(false);
        $manager->persist($user);
        $manager->flush();
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
