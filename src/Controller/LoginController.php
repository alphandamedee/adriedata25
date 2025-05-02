<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur pour gérer l'authentification des utilisateurs
 */
class LoginController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et gère la tentative de connexion
     * 
     * @param AuthenticationUtils $authenticationUtils Utilitaires d'authentification Symfony
     * @param Request $request La requête HTTP
     * @return Response Page de connexion avec les éventuelles erreurs
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        // Récupération des erreurs d'authentification s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Message de déconnexion s'il existe
        $logoutMessage = $request->getSession()->getFlashBag()->get('logout_message');

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'logout_message' => $logoutMessage,
        ]);
    }

    /**
     * Gère la déconnexion de l'utilisateur
     * Cette méthode est interceptée par le firewall de sécurité
     * 
     * @throws \Exception Cette exception n'est jamais lancée car la route est interceptée
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('Cette méthode ne devrait jamais être exécutée - elle est interceptée par le pare-feu de sécurité.');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
?>