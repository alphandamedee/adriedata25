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

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
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
            // Gérer le mot de passe
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            // Gérer l'upload de photo
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

        dd($this->getUser());

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

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
            // Mettre à jour le mot de passe si rempli
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            // Gérer upload photo
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

    #[Route('/list', name: 'admin_user_list')]
        public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/reset-password/{id}', name: 'admin_user_reset_password')]
    public function resetPassword(
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        $newPassword = bin2hex(random_bytes(4)); // ex: a3f9b2d1
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $em->flush();
    
        // Envoi de l'email
        $email = (new Email())
            ->from('admin@votresite.com')
            ->to($user->getEmail()) // fonctionne car getEmail() existe
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
    
        // Message pour l'admin
        $this->addFlash('success', "Mot de passe réinitialisé et envoyé à <strong>{$user->getEmail()}</strong>");
    
        return $this->redirectToRoute('admin_user_list');
    }

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

    #[IsGranted('ROLE_ADMIN')]
   
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(
        UserRepository $userRepository,
        ProduitRepository $produitRepository,
        InterventionRepository $interventionRepository
    ): Response {
        $nbUsers = $userRepository->count([]);
        $nbAdmins = $userRepository->countByRole('Administrateur');
        $nbTechniciens = $userRepository->countByRole('Technicien');
        $nbBenevoles = $userRepository->countByRole('Bénévole');
    
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