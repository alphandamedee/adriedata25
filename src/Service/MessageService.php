<?php

namespace App\Service;

use App\Repository\MessageRepository;
use Symfony\Bundle\SecurityBundle\Security;

class MessageService
{
    public function __construct(
        private MessageRepository $messageRepository,
        private Security $security
    ) {}

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();
        if (!$user) return 0;
        
        return $this->messageRepository->countUnreadMessages($user);
    }
}
