<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RedirectController extends AbstractController
{
    #[Route('/redirect-after-login', name: 'redirect_after_login')]
    public function redirectAfterLogin(Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($this->isGranted('ROLE_TECHNICIEN')) {
            return $this->redirectToRoute('technicien_dashboard');
        }

        if ($this->isGranted('ROLE_BENEVOLE')) {
            return $this->redirectToRoute('benevole_dashboard');
        }

        return $this->redirectToRoute('default_route'); // Fallback route
    }
}
