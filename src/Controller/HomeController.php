<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    public function someLayoutSharedAction(MessageRepository $repo, Security $security)
    {
        $user = $security->getUser();
        $unreadCount = $repo->countUnreadMessagesForUser($user);

        return $this->render('base.html.twig', [
            'unreadCount' => $unreadCount
        ]);
    }

}