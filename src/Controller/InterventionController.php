<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Produit;
use App\Form\InterventionType;
use App\Repository\InterventionRepository;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/intervention")
 */
class InterventionController extends AbstractController
{
    private $interventionRepository;

    public function __construct(InterventionRepository $interventionRepository)
    {
        $this->interventionRepository = $interventionRepository;
    }

    #[Route('/new/{id}', name: 'intervention_new')]
    public function new(Request $request, Produit $produit, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, Security $security, int $id): Response
    {
        $user = $security->getUser(); // Récupère l'utilisateur connecté
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une intervention.');
        }
        $intervention = new Intervention();
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Pré-remplir les champs de l'intervention avec les données du produit
        $intervention->setProduit($produit);
        $intervention->setCodeBarre($produit->getCodeBarre());
        $intervention->setCategorie($produit->getCategorie());
        $intervention->setMarque($produit->getMarque());
        $intervention->setModele($produit->getModele());
        $intervention->setTaille($produit->getTaille());    
        $intervention->setNumeroSerie($produit->getNumeroSerie());
        $intervention->setCpu($produit->getCpu());
        $intervention->setFrequenceCpu($produit->getFrequenceCpu());
        $intervention->setStatut($produit->getStatut());
        $intervention->setRam($produit->getRam());
        $intervention->setTypeRam($produit->getTypeRam());
        $intervention->setStockage($produit->getStockage());
        $intervention->setTypeStockage($produit->getTypeStockage());
        $intervention->setCarteGraphique($produit->getCarteGraphique());
        $intervention->setMemoireVideo($produit->getMemoireVideo());
        $intervention->setIntervenant($user); // Assigner l'intervenant
       
        $form = $this->createForm(InterventionType::class, $intervention, [
            'intervenant' => $user,
        ]);
        $form->handleRequest($request);
            
        if ($form->isSubmitted() && $form->isValid()) {

            // Ces champs sont déjà des booléens, donc pas besoin de les convertir
            // Mettre à jour les champs de l'intervention avec les valeurs des checkboxes
            $intervention->setMiseAJourWindows($intervention->getMiseAJourWindows() ? true : false);
            $intervention->setMiseAJourPilotes($intervention->getMiseAJourPilotes() ? true : false);
            $intervention->setAutresLogiciels($intervention->getAutresLogiciels() ? true : false);
            
            if ($intervention->getMiseAJourWindows()) {
                $produit->setMiseAJourWindows(new \DateTime());
            }
            if ($intervention->getMiseAJourPilotes()) {
                $produit->setMiseAJourPilotes(new \DateTime());
            }
            if ($intervention->getAutresLogiciels()) {
                $produit->setAutresLogiciels(new \DateTime());
            }

            // Persist changes to Produit
            $entityManager->persist($produit);

            // Générer et enregistrer le chemin du PDF
            $pdfFilePath = $this->generatePdf($intervention);
           $intervention->setPdfFilePath($pdfFilePath);

            // Mettre à jour les champs statut et codeEtagere du produit
            $produit->setStatut($intervention->getStatut());
            $produit->setCodeEtagere($intervention->getCodeEtagere());
            $produit->setRam($intervention->getRam());
            // Ajoute avant setTypeRam pour éviter l'erreur de type
            $typeRamEntity = $entityManager->getRepository(\App\Entity\TypeRam::class)->findOneBy([
                'nom' => $intervention->getTypeRam()
            ]);
            $produit->setTypeRam($typeRamEntity);
            // $produit->setTypeRam($intervention->getTypeRam());
            $produit->setModele($intervention->getModele());
            $produit->setMarque($intervention->getMarque());
            $produit->setTaille($intervention->getTaille());    
            $produit->setStockage($intervention->getStockage());
            $produit->setTypeStockage($intervention->getTypeStockage());
            $produit->setNumeroSerie($intervention->getNumeroSerie());
            $produit->setCarteGraphique($intervention->getCarteGraphique());
            $produit->setMemoireVideo($intervention->getMemoireVideo());
            $produit->setCpu($intervention->getCpu());
            $produit->setFrequenceCpu($intervention->getFrequenceCpu());

            
            $entityManager->persist($intervention);
            $entityManager->flush(); // Sauvegarde l'intervention pour obtenir son ID

            $this->addFlash('success', 'Intervention enregistrée et produit mis à jour avec succès.');
            return $this->redirectToRoute('intervention_show', ['id' => $intervention->getId()]);
        }

        return $this->render('intervention/new.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'intervention' => $intervention,
            'intervenant' => $user->getPrenom() . ' ' . $user->getNomUser(),
            'intervent' => $user,
            'dateIntervention' => $intervention->getDateIntervention(),
        ]);
    }

    #[Route('/nouvelle', name: 'intervention_nouvelle')]
    public function interventionForm(
        Request $request,
        EntityManagerInterface $em,
        ProduitRepository $produitRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté.");
        }

        $intervention = new Intervention();
        $intervention->setIntervenant($user);

        $produit = null;

        // 💡 1. Récupération par ID (via query param)
        if ($request->query->has('id')) {
            $produit = $produitRepository->find($request->query->get('id'));
        }

        // 💡 2. Récupération par code-barre
        if (!$produit && $request->query->has('codeBarre')) {
            $produit = $produitRepository->findOneBy(['codeBarre' => $request->query->get('codeBarre')]);
        }

        // ✅ Remplir l'intervention avec les données produit si trouvé
        if ($produit) {
            $intervention->setProduit($produit); // 🔴 Important pour éviter l'erreur SQL
            $intervention->setCodeBarre($produit->getCodeBarre());
            $intervention->setCategorie($produit->getCategorie());
            $intervention->setMarque($produit->getMarque());
            $intervention->setModele($produit->getModele());
            $intervention->setTaille($produit->getTaille());     
            $intervention->setNumeroSerie($produit->getNumeroSerie());
            $intervention->setCpu($produit->getCpu());
            $intervention->setFrequenceCpu($produit->getFrequenceCpu());
            $intervention->setRam($produit->getRam());
            $intervention->setTypeRam($produit->getTypeRam());
            $intervention->setStockage($produit->getStockage());
            $intervention->setTypeStockage($produit->getTypeStockage());
            $intervention->setCarteGraphique($produit->getCarteGraphique());
            $intervention->setMemoireVideo($produit->getMemoireVideo());
        }

        $form = $this->createForm(InterventionType::class, $intervention, [
            'intervenant' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($intervention);
            $em->flush();

            // Générer le PDF
            $pdfFilePath = $this->generatePdf($intervention);
            $intervention->setPdfFilePath($pdfFilePath);
            $em->flush();

            return $this->redirectToRoute('intervention_show', ['id' => $intervention->getId()]);
        }

        return $this->render('intervention/new.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'intervenant' => $user->getPrenom() . ' ' . $user->getNomUser(),
        ]);
    }

    public function generatePdf(Intervention $intervention): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Remove barcode generation code
        $barcodeBase64 = null;

        $html = $this->renderView('intervention/pdf_template.html.twig', [
            'intervention' => $intervention,
            'produit' => $intervention->getProduit(), // Pass the produit variable
            'barcode' => $barcodeBase64
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/interventions/';
        if (!file_exists($pdfPath)) {
            mkdir($pdfPath, 0777, true);
        }

        $codeBarre = $intervention->getCodeBarre() ?: 'unknown';
        $intervenantId = $intervention->getIntervenant() ? $intervention->getIntervenant()->getId() : 'unknown';
        $date = $intervention->getDateIntervention()->format('Ymd');
        $pdfFileName = sprintf('%s-%s-%s.pdf', $date, $codeBarre, $intervenantId);
        $fullPdfPath = $pdfPath . $pdfFileName;

        file_put_contents($fullPdfPath, $dompdf->output());

        return '/uploads/interventions/' . $pdfFileName;
        //return null;
    }

    #[Route('/delete-multiple', name: 'intervention_delete_multiple', methods: ['POST'])]
    public function deleteMultiple(Request $request, EntityManagerInterface $em): Response
    {
        $ids = $request->request->all('ids');

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $intervention = $em->getRepository(Intervention::class)->find($id);
                if ($intervention) {
                    $em->remove($intervention);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Intervention(s) supprimée(s) avec succès.');
        } else {
            $this->addFlash('warning', 'Aucune intervention sélectionnée.');
        }

        return $this->redirectToRoute('app_profil');
    }



    #[Route('/confirm-pdf/{id}', name: 'intervention_confirm_pdf')]
    public function confirmPdf(Intervention $intervention, EntityManagerInterface $entityManager): Response
    {
        $pdfPath = $this->generatePdf($intervention);
        $intervention->setPdfFilePath($pdfPath);
        $entityManager->flush();

        $this->addFlash('success', 'PDF généré avec succès.');

        return $this->redirectToRoute('intervention_show', [
            'id' => $intervention->getId()
        ]);
    }

    #[Route('/{id}', name: 'intervention_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Intervention $intervention): Response
    {
        $form = $this->createForm(InterventionType::class, $intervention);

        return $this->render('intervention/show.html.twig', [
            'intervention' => $intervention,
            'form' => $form->createView(),
            'produit' => $intervention->getProduit(),
        ]);
        
    }

    #[Route('/list', name: 'intervention_list')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function list(InterventionRepository $interventionRepository, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $interventions = $interventionRepository->findAll();
        } else {
            $interventions = $interventionRepository->findBy(['intervenant' => $user]);
        }

        return $this->render('intervention/list.html.twig', [
            'interventions' => $interventions,
        ]);
    }

    // Route pour obtenir un produit par son code-barres
    #[Route("/get-product-by-code", name: "get_product_by_code_barre", methods: ["GET"])]
    public function getProductByCodeBarre(Request $request, ProduitRepository $produitRepository): Response
    {
        // Récupère le code-barres depuis la requête
        $codeBarre = $request->query->get('codeBarre');
        // Recherche le produit correspondant au code-barres
        $produit = $produitRepository->findOneBy(['codeBarre' => $codeBarre]);

        // Vérifie si le produit n'est pas trouvé
        if (!$produit) {
            // Retourne une réponse JSON avec un message d'erreur
            return $this->json(['success' => false, 'message' => 'Produit non trouvé'], 404);
        }

        // Retourne une réponse JSON avec les détails du produit
        return $this->json([
            'success' => true,
            'produit' => [
                'categorie' => $produit->getCategorie(),
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
                'memoireVideo' => $produit->getMemoireVideo(),
                'systemeExploitation' => $produit->getSystemeExploitation(),
                
            ]
        ]);
    }

    #[Route('/intervention/pdf/{id}', name: 'intervention_pdf')]
    public function genererPdf(Intervention $intervention): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Permet d'afficher les images et CSS externes

        $dompdf = new Dompdf($options);

        // Générer le HTML depuis `show.html.twig`, en passant `is_pdf = true`
        $html = $this->renderView('intervention/show.html.twig', [
            'intervention' => $intervention,
            'is_pdf' => true // Permet de masquer les boutons et autres éléments interactifs
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="intervention_'.$intervention->getId().'.pdf"',
            ]
        );
    }

    #[Route('/api/dashboard-data', name: 'api_dashboard_data', methods: ['GET'])]
    public function getDashboardData(
        Request $request,
        InterventionRepository $interventionRepository,
        UserRepository $userRepository,
        Security $security
    ): JsonResponse {
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $filter = $request->query->get('filter', 'my');

        // 🔒 Sécurité : ne permettre que les filtres 'my' ou 'all'
        if (!in_array($filter, ['my', 'all'])) {
            $filter = 'my';
        }

        // 📅 Conversion des dates avec gestion d'erreurs
        try {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $endDate->setTime(23, 59, 59);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Dates invalides'], 400);
        }

        // 🔐 Vérification que l'utilisateur est bien connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 403);
        }

        // 📦 Récupération des interventions dans la période avec filtre utilisateur
        $interventions = $interventionRepository->findByDateRange($startDate, $endDate, $filter === 'my' ? $user : null);

        // 🏷️ Initialisation des étiquettes (labels) par jour
        $labels = [];
        $interventionsPerDay = [];
        $productsPerDay = [];             // 📊 Produits uniques par jour
        $produitsUniques = [];           // 🔢 Tous les produits uniques globalement
        $produitsParDate = [];           // 🗓️ Stocke les ID produits par jour pour éviter doublons

        // 📅 Construction de la période jour par jour
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), (clone $endDate)->modify('+1 day'));
        foreach ($period as $date) {
            $label = $date->format('Y-m-d');
            $labels[] = $label;
            $interventionsPerDay[$label] = 0;
            $produitsParDate[$label] = [];  // Initialiser pour chaque jour
        }

        // 🔁 Boucle sur les interventions pour remplir les compteurs
        foreach ($interventions as $intervention) {
            $date = $intervention->getDateIntervention()->format('Y-m-d');
            $interventionsPerDay[$date]++;

            if ($intervention->getProduit()) {
                $produitId = $intervention->getProduit()->getIdProduit();

                // ✅ Ajouter uniquement si le produit n’a pas encore été compté pour ce jour
                $produitsParDate[$date][$produitId] = true;

                // ✅ Stocker globalement pour le total unique
                $produitsUniques[$produitId] = true;
            }
        }

        // 🧮 Compter les produits uniques par jour
        foreach ($labels as $label) {
            $productsPerDay[$label] = count($produitsParDate[$label]);
        }

        // ✅ Réponse JSON pour le frontend
        return new JsonResponse([
            'labels' => $labels,
            'interventionsData' => array_values($interventionsPerDay),
            'productsData' => array_values($productsPerDay),
            'interventionsCount' => count($interventions),
            'uniqueProductsCount' => count($produitsUniques),
        ]);
    }


    #[Route('/dashboard', name: 'user_dashboard')]
    public function dashboard(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = [];
        if ($this->isGranted('ROLE_ADMIN')) {
            $users = $userRepository->findAll();
        }

        return $this->render('user/dashboard.html.twig', [
            'users' => $users
        ]);
    }


    #[Route('/upload-image', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['fileName']) || !isset($data['imageData'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $fileName = $data['fileName'];
        $imageData = $data['imageData'];

        // Décoder l'image base64
        $imageData = explode(',', $imageData)[1];
        $imageData = base64_decode($imageData);

        // Définir le chemin d'enregistrement
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filePath = $uploadDir . '/' . $fileName;

        // Enregistrer l'image
        file_put_contents($filePath, $imageData);

        return new JsonResponse(['success' => true, 'filePath' => '/uploads/' . $fileName]);
    }
}