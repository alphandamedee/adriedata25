<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

/**
 * Contrôleur pour gérer la réinitialisation des mots de passe oubliés
 */
class ForgotPasswordController extends AbstractController
{
    /**
     * Affiche et traite le formulaire de demande de réinitialisation
     * 
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param MailerInterface $mailer Service d'envoi d'emails
     * @return Response Page du formulaire ou redirection
     */
    #[Route('/mot-de-passe-oublie', name: 'forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $em->getRepository(User::class)->findOneBy(['mailUser' => $email]);

            if ($user) {
                // Génération d'un token unique
                $token = Uuid::v4()->toRfc4122();
                $user->setResetToken($token);
                $em->flush();

                // Création du lien de réinitialisation
                $resetUrl = $this->generateUrl('reset_password', ['token' => $token], 0);

                // Envoi de l'email avec le lien
                $emailMessage = (new Email())
                    ->from('noreply@tonsite.com')
                    ->to($user->getMailUser())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html('<p>Bonjour,</p><p>Pour réinitialiser votre mot de passe, cliquez sur le lien suivant : 
                        <a href="' . $resetUrl . '">Réinitialiser</a></p>');

                $mailer->send($emailMessage);
                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
            } else {
                $this->addFlash('danger', 'Aucun utilisateur trouvé avec cette adresse email.');
            }
        }

        return $this->render('security/forgot_password.html.twig');
    }

    /**
     * Traite la réinitialisation du mot de passe avec le token reçu
     * 
     * @param Request $request La requête HTTP
     * @param string $token Le token de réinitialisation
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe
     * @return Response Page de réinitialisation ou redirection
     */
    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword(Request $request, string $token, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Recherche de l'utilisateur par son token
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Jeton invalide.');
            return $this->redirectToRoute('forgot_password');
        }

        if ($request->isMethod('POST')) {
            // Mise à jour du mot de passe
            $newPassword = $request->request->get('password');
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null); // Invalidation du token
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig');
    }
}
