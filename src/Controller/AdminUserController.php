<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\InterventionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Contrôleur pour la gestion des utilisateurs par l'administrateur
 * Toutes les routes nécessitent le rôle ROLE_ADMIN
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    /**
     * Ajoute un nouvel utilisateur
     * 
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe
     * @param SluggerInterface $slugger Service de création de slugs
     * @return Response Page d'ajout ou redirection
     */
    #[Route('/add', name: 'admin_user_add')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            // Gestion de la photo de profil
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $filename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($this->getParameter('photo_directory'), $filename);
                $user->setPhoto($filename);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    /**
     * Modifie un utilisateur existant
     * 
     * @param User $user L'utilisateur à modifier
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe
     * @param SluggerInterface $slugger Service de création de slugs
     * @return Response Page de modification ou redirection
     */
    #[Route('/edit/{id}', name: 'admin_user_edit')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(AdminUserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour du mot de passe si fourni
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            // Gestion de la photo de profil
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $filename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($this->getParameter('photo_directory'), $filename);
                $user->setPhoto($filename);
            }

            $em->flush();
            $this->addFlash('success', 'Utilisateur mis à jour.');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }

    /**
     * Liste tous les utilisateurs
     * 
     * @param UserRepository $userRepository Repository des utilisateurs
     * @return Response Page listant les utilisateurs
     */
    #[Route('/list', name: 'admin_user_list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     * Génère un mot de passe aléatoire et l'envoie par email
     * 
     * @param User $user L'utilisateur concerné
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe
     * @param MailerInterface $mailer Service d'envoi d'emails
     * @return Response Redirection vers la liste des utilisateurs
     */
    #[Route('/reset-password/{id}', name: 'admin_user_reset_password')]
    public function resetPassword(
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        // Génération d'un mot de passe aléatoire
        $newPassword = bin2hex(random_bytes(4));
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $em->flush();
    
        // Envoi du nouveau mot de passe par email
        $email = (new Email())
            ->from('admin@votresite.com')
            ->to($user->getEmail())
            ->subject('Votre mot de passe a été réinitialisé')
            ->html("
                <p>Bonjour <strong>{$user->getPrenom()}</strong>,</p>
                <p>Votre mot de passe a été réinitialisé par l'administrateur.</p>
                <p>Voici votre nouveau mot de passe temporaire : <strong>$newPassword</strong></p>
                <p>Merci de vous connecter et de le modifier dès que possible.</p>
                <hr>
                <small>Ce message est automatique, merci de ne pas répondre.</small>
            ");
    
        $mailer->send($email);
        $this->addFlash('success', "Mot de passe réinitialisé et envoyé à <strong>{$user->getEmail()}</strong>");
    
        return $this->redirectToRoute('admin_user_list');
    }

    /**
     * Supprime la photo de profil d'un utilisateur
     * 
     * @param User $user L'utilisateur concerné
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @param Filesystem $fs Service de gestion des fichiers
     * @return Response Redirection vers la page d'édition
     */
    #[Route('/delete-photo/{id}', name: 'admin_user_delete_photo')]
    public function deletePhoto(User $user, EntityManagerInterface $em, Filesystem $fs): Response
    {
        $photoPath = $this->getParameter('photo_directory') . '/' . $user->getPhoto();

        if ($user->getPhoto() && $fs->exists($photoPath)) {
            $fs->remove($photoPath);
            $user->setPhoto(null);
            $em->flush();
            $this->addFlash('success', 'Photo supprimée avec succès.');
        } else {
            $this->addFlash('warning', 'Aucune photo à supprimer.');
        }

        return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
    }

    /**
     * Affiche le tableau de bord administrateur
     * Montre les statistiques globales du système
     * 
     * @param UserRepository $userRepository Repository des utilisateurs
     * @param ProduitRepository $produitRepository Repository des produits
     * @param InterventionRepository $interventionRepository Repository des interventions
     * @return Response Page du tableau de bord
     */
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(
        UserRepository $userRepository,
        ProduitRepository $produitRepository,
        InterventionRepository $interventionRepository
    ): Response {
        // Statistiques des utilisateurs par rôle
        $nbUsers = $userRepository->count([]);
        $nbAdmins = $userRepository->countByRole('Administrateur');
        $nbTechniciens = $userRepository->countByRole('Technicien');
        $nbBenevoles = $userRepository->countByRole('Bénévole');
    
        // Statistiques globales
        $nbProduits = $produitRepository->count([]);
        $nbInterventions = $interventionRepository->count([]);
    
        return $this->render('admin/admin_dashboard_menu.html.twig', [
            'nbUsers' => $nbUsers,
            'nbAdmins' => $nbAdmins,
            'nbTechniciens' => $nbTechniciens,
            'nbBenevoles' => $nbBenevoles,
            'nbProduits' => $nbProduits,
            'nbInterventions' => $nbInterventions,
        ]);
    }

    /**
     * Affiche le tableau de bord technicien
     * 
     * @param ProduitRepository $produitRepository Repository des produits
     * @param InterventionRepository $interventionRepository Repository des interventions
     * @return Response Page du tableau de bord technicien
     */
    #[Route('/technicien/dashboard', name: 'technicien_dashboard')]
    #[IsGranted('ROLE_TECHNICIEN')]
    public function technicienDashboard(
        ProduitRepository $produitRepository,
        InterventionRepository $interventionRepository
    ): Response {
        $nbProduits = $produitRepository->count([]);
        $nbInterventions = $interventionRepository->count([]);
    
        return $this->render('technicien/technicien_dashboard_menu.html.twig', [
            'nbProduits' => $nbProduits,
            'nbInterventions' => $nbInterventions,
        ]);
    }

    /**
     * Affiche le tableau de bord bénévole
     * 
     * @param ProduitRepository $produitRepository Repository des produits
     * @return Response Page du tableau de bord bénévole
     */
    #[Route('/benevole/dashboard', name: 'benevole_dashboard')]
    #[IsGranted('ROLE_BENEVOLE')]
    public function benevoleDashboard(
        ProduitRepository $produitRepository
    ): Response {
        $nbProduits = $produitRepository->count([]);
    
        return $this->render('benevole/benevole_dashboard_menu.html.twig', [
            'nbProduits' => $nbProduits,
        ]);
    }
}