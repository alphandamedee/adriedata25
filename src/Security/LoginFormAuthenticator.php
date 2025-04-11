<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    // private $security;

    // public function __construct(Security $security)
    // {
    //     $this->security = $security;
    // }

    public function authenticate(Request $request): Passport
    {
        $mailUser = $request->request->get('mailUser', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        return new Passport(
            new UserBadge($mailUser),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    /**
     * Cette méthode est appelée après une authentification réussie.
     * Ici, nous redirigeons l'utilisateur vers la liste des produits.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
         // Remplacez 'produit_index' par le nom de votre route correspondant à la page produit.
         return new RedirectResponse($this->urlGenerator->generate('app_profil'));
    }

    /**
     * Détermine l'URL de la page de connexion.
     */
    protected function getLoginUrl(Request $request): string
    {
         // Remplacez 'app_login' par le nom de votre route de connexion
         return $this->urlGenerator->generate('app_login');
    }

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
}