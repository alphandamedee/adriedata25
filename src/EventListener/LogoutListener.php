<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $session = $request->getSession();
            $session->getFlashBag()->add('logout_message', 'Vous avez été déconnecté avec succès.');
        }
    }
}
