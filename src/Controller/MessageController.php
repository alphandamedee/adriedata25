<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MessageController extends AbstractController
{
    #[Route('/{id}', name: 'chat', methods: ['GET', 'POST'])]
    public function chat(
        Request $request,
        User $id,
        EntityManagerInterface $em,
        Security $security,
        MessageRepository $repo
    ): Response {
        $user = $security->getUser();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setExpediteur($user);
            $message->setDestinataire($id);
            $message->setDateEnvoi(new \DateTime());
            $message->setLu(false);

            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('chat', ['id' => $id->getId()]);
        }

        // marquer comme lu tous les messages non lus reçus
        $messages = $repo->createQueryBuilder('m')
            ->where('(m.expediteur = :me AND m.destinataire = :other) OR (m.expediteur = :other AND m.destinataire = :me)')
            ->setParameter('me', $user)
            ->setParameter('other', $id)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($messages as $msg) {
            if ($msg->getDestinataire() === $user && !$msg->isLu()) {
                $msg->setLu(true);
                $em->persist($msg);
            }
        }
        $em->flush();

        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
            'interlocuteur' => $id,
        ]);
    }
}
