<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private UserRepository $userRepo;
    private $router;

    // COMME PAS D'API -> $pdo = API

    public function __construct(private UrlGeneratorInterface $urlGenerator, UserRepository $userRepo, RouterInterface $router)
    {
        $this->router = $router;
        $this->userRepo = $userRepo;
    }

    public function authenticate(Request $request): Passport
    {
        $licence = $request->request->get('licence_code');
        $password = $request->request->get('password');

        //$request->getSession()->set(Security::LAST_USERNAME, $email);
        return new Passport(
            new UserBadge($licence,function($userId){
                $user = $this->userRepo->findOneBy(['numlicence'=>$userId]);
                return $user;
            }),
            new CustomCredentials(function($credential,User $user) {
                return $credential == $user->getPassword();
            }, $password)
            
        );
        new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $this->router->generate('app_accueil')
        );
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }


}
