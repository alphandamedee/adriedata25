<?php

namespace App\Controller;

use App\Entity\CategorieProduit;
use App\Form\CategorieProduitType;
use App\Repository\CategorieProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie-produit')]
class CategorieProduitController extends AbstractController
{
    #[Route('/', name: 'categorie_produit_index', methods: ['GET'])]
    public function index(CategorieProduitRepository $categorieProduitRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieProduitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'categorie_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieProduit = new CategorieProduit();
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieProduit);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('categorie_produit_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'categorie_produit_show', methods: ['GET'])]
    public function show(CategorieProduit $categorieProduit): Response
    {
        return $this->render('categorie_produit/show.html.twig', [
            'categorie' => $categorieProduit,
        ]);
    }

    #[Route('/{id}/edit', name: 'categorie_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('categorie_produit_index');
        }

        return $this->render('categorie_produit/edit.html.twig', [
            'categorie' => $categorieProduit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'categorie_produit_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieProduit $categorieProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieProduit->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($categorieProduit);
                $entityManager->flush();
                $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des produits.');
            }
        }

        return $this->redirectToRoute('categorie_produit_index');
    }

    
    #[Route('/populate', name: 'categorie_produit_populate', methods: ['GET'])]
    public function populateCategories(EntityManagerInterface $entityManager): Response
    {
        $categories = [
            'Portable', 'Ecran', 'Unité Centrale', 'ADAPTATEUR', 'Alarme', 
            'ALL IN ONE', 'AMPLI', 'APPLE', 'Clé USB', 'Disque Dur', 
            'Enceinte', 'GPS', 'Imprimante', 'iphone', 'MAC', 
            'Machine à Coudre', 'MINI PC', 'NAS', 'Onduleur', 'Photocopieur', 
            'Projecteur', 'PS', 'PS2', 'Routeur', 'Scanner', 'Serveur',
            'Station d1accueil', 'Switch', 'Table', 'Tablette', 'Téléphone',
            'Téléphone Portable', 'TV'
        ];

        $repository = $entityManager->getRepository(CategorieProduit::class);
        $count = 0;

        foreach ($categories as $categoryName) {
            // Vérifier si la catégorie existe déjà pour éviter les doublons
            $existingCategory = $repository->findOneBy(['nom' => $categoryName]);
            if (!$existingCategory) {
                $category = new CategorieProduit();
                $category->setNom($categoryName);
                $entityManager->persist($category);
                $count++;
            }
        }

        $entityManager->flush();
        $this->addFlash('success', $count . ' catégories ont été ajoutées avec succès.');

        return $this->redirectToRoute('categorie_produit_index');
    }
    #[Route('/toggle-visible/{id}', name: 'categorie_toggle_visible')]
    public function toggleVisible(Request $request, CategorieProduit $categorie, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $categorie->getId(), $request->request->get('_token'))) {
            $categorie->setVisible(!$categorie->isVisible());
            $em->flush();
        }

        return $this->redirectToRoute('categorie_produit_index');
    }

}