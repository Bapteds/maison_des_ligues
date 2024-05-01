<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityController extends AbstractController
{

    private $api_url;

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->api_url = $_ENV['API_URL'];
        $this->mailer = $mailer;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error instanceof CustomUserMessageAuthenticationException) {
            $error = $error->getMessage();
        } elseif (isset($error) && $error->getCode() == 0) {
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
                        $token = bin2hex(random_bytes(16));
                        $link = $this->generateUrl('app_route_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                        $password = password_hash($password,PASSWORD_DEFAULT);
                        $this->createUser($licencie, $password, $manager, $token);
                        $this->mailer->sendEmail('maisondesligues@gouv.fr', $licencie['mail'], 'Validation de compte', $link);
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

    #[Route(path: '/confirm', name: 'app_route_confirm')]
    public function confirmAccount(Request $request, UserRepository $repo)
    {
        $token = $request->get('token');
        $isValid = $repo->verifyUser($token);
        if ($isValid) {
            return $this->render('admin/auth/login.html.twig', ['error' => 'Compte validée, vous pouvez vous connecter.']);
        }
        return $this->render('admin/auth/register.html.twig', ['error' => 'Une erreur est survenue, refaite une demande.']);
    }

    #[Route(path: '/forgotpwd', name: 'app_forgot')]
    public function forgotPassword(Request $request, UserRepository $repo, EntityManagerInterface $manager)
    {
        if ($request->isMethod('POST')) {
            $numlicence = $request->get('licence_code');
            $user = $this->getUserByLicence($numlicence,$repo);
            if($user){
                if(!$user->isVerified()){
                    return $this->render('admin/auth/pwd.html.twig',['error' => 'Veuillez vérifier votre compte avant de réinitialiser votre mot de passe.']);
                }elseif($user->isVerified()){
                    $token = bin2hex(random_bytes(16));
                    $link = $this->generateUrl('app_route_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                    $user->setValidToken($token);
                    $repo->save($user,true);
                    $this->mailer->sendEmail('maisondesligues@gouv.fr', $user->getEmail(), 'Changement de mot de passe', $link);
                }
            }
            return $this->render('admin/auth/pwd.html.twig',['error' => 'Si votre licence existe, vous recevrez un mail.']);
        }
    return $this->render('admin/auth/pwd.html.twig',['error' => null]);
    }

    #[Route(path: '/resetpwd', name: 'app_route_reset')]
    public function resetpwd(Request $request, UserRepository $repo){
        $token = $request->get('token');
        if($request->isMethod('POST')){
            $password = $request->request->get('password');
            if($password == $request->request->get('confirmation')){ 
                $password = password_hash($password,PASSWORD_DEFAULT);
                $repo->updatePassword($token,$password);
            }
            else{
                return $this->render('admin/auth/changepwd.html.twig',['error' => 'Les mots de passe ne correspondent pas.']);
            }
        }
        elseif(!$repo->isValidToken($token)){
            return $this->render('admin/auth/changepwd.html.twig',['error' => null]);
        }

        return $this->redirectToRoute('app_login');
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
        //return $this->licenceBDD($numlicence, $manager);
        return $this->licenceAPI($numlicence, $client);
    }


    /**
     * Fonction qui fait appel à l'API, pour récupérer une Licence
     *
     * @param string $numlicence
     * @param HttpClientInterface $client
     * @return array
     */
    private function licenceAPI(string $numlicence, HttpClientInterface $client): array
    {
        $response = $client->request(
            'GET',
            $this->api_url . '/api/licencies?page=1&numlicence=' . $numlicence
        );
        $json = json_decode($response->getContent(), true);
        if (!isset($json['hydra:member'][0])) {
            return [];
        }
        return $json['hydra:member'][0];
    }

    /**
     * A utiliser quand l'API ne fonctionne pas.
     *
     * @param string $numlicence
     * @param EntityManager $manager
     * @return void
     */
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
    private function createUser(array $licencie, string $password, EntityManager $manager, $token)
    {
        $user = new User();
        $user->setEmail($licencie['mail']);
        $user->setRoles('ROLE_INSCRIT');
        $user->setPassword($password);
        $user->setNumlicence($licencie['numlicence']);
        $user->setIsVerified(false);
        $user->setValidToken($token);
        $manager->persist($user);
        $manager->flush();
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
