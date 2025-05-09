<?php

namespace App\Controller;

use App\Entity\TypeRam;
use App\Form\TypeRamType;
use App\Repository\TypeRamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour gérer les types de mémoire RAM
 */
#[Route('/type-ram')]
class TypeRamController extends AbstractController
{
        #[Route('/', name: 'type_ram_index', methods: ['GET'])]
    public function index(TypeRamRepository $typeRamRepository): Response
    {
        return $this->render('typeram/index.html.twig', [
            'types_ram' => $typeRamRepository->findAll(),
        ]);
    }

        #[Route('/new', name: 'type_ram_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeRam = new TypeRam();
        $form = $this->createForm(TypeRamType::class, $typeRam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeRam);
            $entityManager->flush();

            $this->addFlash('success', 'Le type de RAM a été créé avec succès.');
            return $this->redirectToRoute('type_ram_index');
        }

        return $this->render('typeram/new.html.twig', [
            'type_ram' => $typeRam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un type de RAM
     * 
     * @param TypeRam $typeRam Le type de RAM à afficher
     * @return Response Page de détails du type de RAM
     */
    #[Route('/{id}', name: 'type_ram_show', methods: ['GET'])]
    public function show(TypeRam $typeRam): Response
    {
        return $this->render('typeram/show.html.twig', [
            'type_ram' => $typeRam,
        ]);
    }

    /**
     * Modifie un type de RAM existant
     * 
     * @param Request $request La requête HTTP
     * @param TypeRam $typeRam Le type de RAM à modifier
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Page de modification ou redirection
     */
    #[Route('/{id}/edit', name: 'typeram_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeRam $typeRam, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeRamType::class, $typeRam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le type de RAM a été modifié avec succès.');
            return $this->redirectToRoute('type_ram_index');
        }

        return $this->render('typeram/edit.html.twig', [
            'type_ram' => $typeRam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un type de RAM
     * 
     * @param Request $request La requête HTTP
     * @param TypeRam $typeRam Le type de RAM à supprimer
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Redirection vers la liste des types de RAM
     */
    #[Route('/{id}', name: 'type_ram_delete', methods: ['POST'])]
    public function delete(Request $request, TypeRam $typeRam, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeRam->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($typeRam);
                $entityManager->flush();
                $this->addFlash('success', 'Le type de RAM a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer ce type de RAM car il est utilisé par des produits.');
            }
        }

        return $this->redirectToRoute('type_ram_index');
    }

    /**
     * Bascule la visibilité d'un type de RAM
     * 
     * @param Request $request La requête HTTP
     * @param TypeRam $typeRam Le type de RAM à modifier
     * @param EntityManagerInterface $em Gestionnaire d'entités
     * @return Response Redirection vers la liste des types de RAM
     */
    #[Route('/toggle-visible/{id}', name: 'typeram_toggle_visible')]
    public function toggleVisible(Request $request, TypeRam $typeRam, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $typeRam->getId(), $request->request->get('_token'))) {
            $typeRam->setVisible(!$typeRam->isVisible());
            $em->flush();
        }

        return $this->redirectToRoute('type_ram_index');
    }
}