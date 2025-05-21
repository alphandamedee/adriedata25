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

        $users = $messageRepo->findOtherUsers($currentUser);
        $unreadCountsByUserId = $messageRepo->getUnreadCountBySender($currentUser);

        return $this->render('message/index.html.twig', [
            'users' => $users,
            'unreadCounts' => $unreadCountsByUserId
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
        // Marquer les messages comme lus
        $messageRepo->markMessagesAsRead($destinataire, $currentUser);

        return $this->render('message/conversation.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
            'destinataire' => $destinataire
        ]);
    }

    #[Route('/api/unread', name: 'api_unread_count', methods: ['GET'])]
    public function getUnreadCount(MessageRepository $messageRepo): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                return $this->json([
                    'error' => 'Utilisateur non authentifié'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $count = $messageRepo->countUnreadMessages($user);
            return $this->json([
                'success' => true,
                'unread' => $count
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de la récupération des messages',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
