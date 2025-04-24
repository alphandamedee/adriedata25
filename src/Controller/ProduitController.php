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
        // Récupération de la recherche et de la catégorie
        $search = $request->query->get('search', '');
        $categorieId = $request->query->get('categorie');
        $limit = $request->query->getInt('limit', 100); // valeur par défaut
        
        // On utilise le QueryBuilder pour construire la requête de manière dynamique
        $queryBuilder = $entityManager->getRepository(Produit::class)->createQueryBuilder('p')
        ->leftJoin('p.categorie', 'c')         // 🔗 JOINTURE pour charger les catégories
        ->addSelect('c');                      // 🔍 Assure le SELECT complet de la relation
        
        // On ajoute la recherche si elle est présente
        if ($search) {
            $queryBuilder->where('p.codeBarre LIKE :search')
            ->orWhere('c.nom LIKE :search')    // 👈 on recherche aussi dans le nom de la catégorie
            ->orWhere('p.marque LIKE :search')
            ->orWhere('p.modele LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }
        // 🔍 On peut filtrer par catégorie si besoin
        if ($categorieId) {
            $queryBuilder->andWhere('c.id = :catId')->setParameter('catId', $categorieId);
        }

        // On ajoute l'ordre de tri par défaut
        $queryBuilder->orderBy('p.codeBarre', 'ASC');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $limit
        );
        // On ajoute la pagination à la vue
        return $this->render('produit/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    //#Route('/produit/ajouter', name: 'produit_ajouter', methods: ['GET', 'POST'])
    // Route pour ajouter un produit
    #[Route('/produit/ajouter', name: 'produit_ajouter')]
    public function ajouter(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $existant = $produitRepository->findOneBy(['codeBarre' => $produit->getCodeBarre()]);
                if ($existant) {
                    return new JsonResponse([
                        'status' => 'exists',
                        'message' => 'Ce code-barres existe déjà dans la base.'
                    ]);
                }

                try {
                    $em->persist($produit);
                    $em->flush();

                    return new JsonResponse([
                        'status' => 'success',
                        'message' => 'Produit ajouté avec succès.'
                    ]);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'status' => 'error',
                        'message' => 'Une erreur est survenue lors de l\'enregistrement.'
                    ], 500);
                }
            } else {
                // Retourner les erreurs de validation si le formulaire n'est pas valide
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'status' => 'validation_error',
                    'message' => 'Le formulaire contient des erreurs',
                    'errors' => $errors
                ], 400);
            }
        }

        return $this->render('produit/ajouter.html.twig', [
            'form' => $form->createView()
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
                'taille' => $produit->getTaille(),
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
            fputcsv($handle, ['ID', 'Code Barre', 'Catégorie', 'Marque', 'Modèle', 'Taille', 'Stockage', 'RAM', 'Statut', 'Code Étagère']);

            foreach ($produits as $produit) {
                fputcsv($handle, [
                    $produit->getIdProduit(),
                    $produit->getCodeBarre(),
                    $produit->getCategorie() ? $produit->getCategorie()->getNom() : 'N/A',
                    $produit->getMarque(),
                    $produit->getModele(),
                    $produit->getTaille(),
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
        $sheet->setCellValue('F1', 'Taille');
        $sheet->setCellValue('G1', 'Stockage');
        $sheet->setCellValue('H1', 'RAM');
        $sheet->setCellValue('I1', 'Statut');
        $sheet->setCellValue('J1', 'Code Étagère');

        // Populate the data rows
        $row = 2;
        foreach ($produits as $produit) {
            $sheet->setCellValue('A' . $row, $produit->getIdProduit());
            $sheet->setCellValue('B' . $row, $produit->getCodeBarre());
            $sheet->setCellValue('C' . $row, $produit->getCategorie() ? $produit->getCategorie()->getNom() : 'N/A');
            $sheet->setCellValue('D' . $row, $produit->getMarque());
            $sheet->setCellValue('E' . $row, $produit->getModele());
            $sheet->setCellValue('F' . $row, $produit->getTaille());
            $sheet->setCellValue('G' . $row, $produit->getStockage() . ' ' . $produit->getTypeStockage());
            $sheet->setCellValue('H' . $row, $produit->getRam() . ' ' . $produit->getTypeRam());
            $sheet->setCellValue('I' . $row, $produit->getStatut());
            $sheet->setCellValue('J' . $row, $produit->getCodeEtagere());
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
    //Route AJAX pour la recherche de produit
    #[Route('/produit/autocomplete', name: 'produit_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, ProduitRepository $produitRepository): JsonResponse
    {
        $query = $request->query->get('term');
        $produits = $produitRepository->createQueryBuilder('p')
            ->where('p.codeBarre LIKE :query OR p.modele LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $results = [];

        foreach ($produits as $produit) {
            $results[] = [
                'id' => $produit->getIdProduit(),
                'label' => $produit->getCodeBarre() . ' - ' . $produit->getModele(),
            ];
        }

        return $this->json($results);
    }
}
?>
