<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'messages_index')]
    public function index(MessageRepository $messageRepo): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('message/index.html.twig', [
            'users' => $messageRepo->findOtherUsers($currentUser)
        ]);
    }

    #[Route('/user/{id}', name: 'messages_user', methods: ['GET', 'POST'])]
    public function conversation(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        MessageRepository $messageRepo
    ): Response {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $destinataire = $em->getRepository(User::class)->find($id);
        if (!$destinataire) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            'current_user' => $currentUser
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setExpediteur($currentUser)
                   ->setDestinataire($destinataire)
                   ->setDateEnvoi(new \DateTime())
                   ->setLu(false);

            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('messages_user', ['id' => $destinataire->getId()]);
        }

        $messages = $messageRepo->findConversation($currentUser, $destinataire);

        return $this->render('message/conversation.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
            'destinataire' => $destinataire
        ]);
    }

    #[Route('/api/unread', name: 'api_unread_count', methods: ['GET'])]
    public function getUnreadCount(MessageRepository $messageRepo): JsonResponse
    {
        $count = $messageRepo->countUnreadMessages($this->getUser());
        return $this->json(['unread' => $count]);
    }
}
