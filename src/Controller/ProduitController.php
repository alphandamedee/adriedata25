<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');

        $queryBuilder = $entityManager->getRepository(Produit::class)->createQueryBuilder('p')
            ->leftJoin('p.categorie', 'c')         // 🔗 JOINTURE pour charger les catégories
            ->addSelect('c');                      // 🔍 Assure le SELECT complet de la relation

        if ($search) {
            $queryBuilder->where('p.codeBarre LIKE :search')
                ->orWhere('c.nom LIKE :search')    // 👈 on recherche aussi dans le nom de la catégorie
                ->orWhere('p.marque LIKE :search')
                ->orWhere('p.modele LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('produit/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }


    #[Route('/produit/ajouter', name: 'produit_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $em, ProduitRepository $produitRepo): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $codeBarre = $produit->getCodeBarre();

            // 🔒 Vérifie si le code-barre existe déjà
            if ($produitRepo->findOneBy(['codeBarre' => $codeBarre])) {
                $this->addFlash('danger', 'Ce code-barre est déjà utilisé pour un autre produit.');
                return $this->render('produit/ajouter.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/produit/modifier/{id}', name: 'produit_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/supprimer/{id}', name: 'produit_supprimer')]
    public function supprimer(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit');
    }

    #[Route('/produit/codebarre', name: 'get_product_by_code_barre', methods: ['GET'])]
    public function getProductByCodeBarre(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $codeBarre = $request->query->get('codeBarre');

        if (!$codeBarre) {
            return new JsonResponse(['success' => false, 'message' => 'Code-barres manquant'], 400);
        }

        $produit = $entityManager->getRepository(Produit::class)->findOneBy(['codeBarre' => $codeBarre]);

        if (!$produit) {
            return new JsonResponse(['success' => false, 'message' => 'Produit non trouvé'], 404);
        }

        return new JsonResponse([
            'success' => true,
            'produit' => [
                'categorie' => $produit->getCategorie() ? $produit->getCategorie()->getNom() : null,
                'marque' => $produit->getMarque(),
                'modele' => $produit->getModele(),
                'numeroSerie' => $produit->getNumeroSerie(),
                'cpu' => $produit->getCpu(),
                'frequenceCpu' => $produit->getFrequenceCpu(),
                'ram' => $produit->getRam(),
                'typeRam' => $produit->getTypeRam(),
                'stockage' => $produit->getStockage(),
                'typeStockage' => $produit->getTypeStockage(),
                'carteGraphique' => $produit->getCarteGraphique(),
                'memoireVideo' => $produit->getMemoireVideo()
            ]
        ]);
    }

    #[Route('/produit/export/{format}', name: 'produit_export')]
    public function export(ProduitRepository $produitRepository, string $format): Response
    {
        $produits = $produitRepository->findAll();

        if ($format === 'csv') {
            return $this->exportCsv($produits);
        } elseif ($format === 'excel') {
            return $this->exportExcel($produits);
        }

        throw $this->createNotFoundException('Format non supporté');
    }

    private function exportCsv(array $produits): Response
    {
        $response = new StreamedResponse(function() use ($produits) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, ['ID', 'Code Barre', 'Catégorie', 'Marque', 'Modèle', 'Stockage', 'RAM', 'Statut', 'Code Étagère']);

            foreach ($produits as $produit) {
                fputcsv($handle, [
                    $produit->getIdProduit(),
                    $produit->getCodeBarre(),
                    $produit->getCategorie() ? $produit->getCategorie()->getNom() : 'N/A',
                    $produit->getMarque(),
                    $produit->getModele(),
                    $produit->getStockage() . ' ' . $produit->getTypeStockage(),
                    $produit->getRam() . ' ' . $produit->getTypeRam(),
                    $produit->getStatut(),
                    $produit->getCodeEtagere()
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits.csv"');

        return $response;
    }

    private function exportExcel(array $produits): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the header row
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Code Barre');
        $sheet->setCellValue('C1', 'Catégorie');
        $sheet->setCellValue('D1', 'Marque');
        $sheet->setCellValue('E1', 'Modèle');
        $sheet->setCellValue('F1', 'Stockage');
        $sheet->setCellValue('G1', 'RAM');
        $sheet->setCellValue('H1', 'Statut');
        $sheet->setCellValue('I1', 'Code Étagère');

        // Populate the data rows
        $row = 2;
        foreach ($produits as $produit) {
            $sheet->setCellValue('A' . $row, $produit->getIdProduit());
            $sheet->setCellValue('B' . $row, $produit->getCodeBarre());
            $sheet->setCellValue('C' . $row, $produit->getCategorie() ? $produit->getCategorie()->getNom() : 'N/A');
            $sheet->setCellValue('D' . $row, $produit->getMarque());
            $sheet->setCellValue('E' . $row, $produit->getModele());
            $sheet->setCellValue('F' . $row, $produit->getStockage() . ' ' . $produit->getTypeStockage());
            $sheet->setCellValue('G' . $row, $produit->getRam() . ' ' . $produit->getTypeRam());
            $sheet->setCellValue('H' . $row, $produit->getStatut());
            $sheet->setCellValue('I' . $row, $produit->getCodeEtagere());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits.xlsx"');

        return $response;
    }
}
?>
