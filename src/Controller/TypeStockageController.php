<?php

namespace App\Controller;

use App\Entity\TypeStockage;
use App\Form\TypeStockageType;
use App\Repository\TypeStockageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour gérer les types de stockage
 */
#[Route('/type-stockage')]
class TypeStockageController extends AbstractController
{
    /**
     * Liste tous les types de stockage
     * 
     * @param TypeStockageRepository $typeStockageRepository Repository des types de stockage
     * @return Response Page listant tous les types de stockage
     */
    #[Route('/', name: 'type_stockage_index', methods: ['GET'])]
    public function index(TypeStockageRepository $typeStockageRepository): Response
    {
        return $this->render('typestockage/index.html.twig', [
            'types_stockage' => $typeStockageRepository->findAll(),
        ]);
    }

    /**
     * Crée un nouveau type de stockage
     * 
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Formulaire de création ou redirection vers la liste
     */
    #[Route('/new', name: 'type_stockage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeStockage = new TypeStockage();
        $form = $this->createForm(TypeStockageType::class, $typeStockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeStockage);
            $entityManager->flush();

            $this->addFlash('success', 'Le type de stockage a été créé avec succès.');
            return $this->redirectToRoute('type_stockage_index');
        }

        return $this->render('typestockage/new.html.twig', [
            'type_stockage' => $typeStockage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un type de stockage
     * 
     * @param TypeStockage $typeStockage Le type de stockage à afficher
     * @return Response Page de détails du type de stockage
     */
    #[Route('/{id}', name: 'type_stockage_show', methods: ['GET'])]
    public function show(TypeStockage $typeStockage): Response
    {
        return $this->render('typestockage/show.html.twig', [
            'type_stockage' => $typeStockage,
        ]);
    }

    /**
     * Modifie un type de stockage existant
     * 
     * @param Request $request La requête HTTP
     * @param TypeStockage $typeStockage Le type de stockage à modifier
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Formulaire d'édition ou redirection vers la liste
     */
    #[Route('/{id}/edit', name: 'type_stockage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeStockage $typeStockage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeStockageType::class, $typeStockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le type de stockage a été modifié avec succès.');
            return $this->redirectToRoute('type_stockage_index');
        }

        return $this->render('typestockage/edit.html.twig', [
            'type_stockage' => $typeStockage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un type de stockage
     * 
     * @param Request $request La requête HTTP
     * @param TypeStockage $typeStockage Le type de stockage à supprimer
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Redirection vers la liste des types de stockage
     */
    #[Route('/{id}', name: 'type_stockage_delete', methods: ['POST'])]
    public function delete(Request $request, TypeStockage $typeStockage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeStockage->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($typeStockage);
                $entityManager->flush();
                $this->addFlash('success', 'Le type de stockage a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer ce type de stockage car il est utilisé par des produits.');
            }
        }

        return $this->redirectToRoute('type_stockage_index');
    }
}