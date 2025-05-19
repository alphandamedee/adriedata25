<?php
namespace App\Twig;

use App\Repository\MessageRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageExtension extends AbstractExtension
{
    private MessageRepository $repo;
    private Security $security;

    public function __construct(MessageRepository $repo, Security $security)
    {
        $this->repo = $repo;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unreadCount', [$this, 'getUnreadCount']),
        ];
    }

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();
        if (!$user) {
            return 0;
        }

        return $this->repo->countUnreadMessagesForUser($user);
    }



    public function someRoute(): Response
    {
        $unreadCount = $this->repo->countUnreadMessages($this->getUser());
        
        return $this->render('xxx.html.twig', [
            'unreadCount' => $unreadCount,
        ]);
    }

}
