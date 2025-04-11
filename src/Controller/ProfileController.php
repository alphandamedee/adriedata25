<?php
namespace App\Controller;

use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\UserRepository;


class ProfileController extends AbstractController
{
    // change

    #[Route('/profil', name: 'app_profil')]
    #[IsGranted('ROLE_USER')]
    public function index(
        UserInterface $user,
        InterventionRepository $interventionRepository,
        UserRepository $userRepository,
        Request $request
    ): Response {
        $view = $request->query->get('view', 'mine');
        $intervenant = $request->query->get('intervenant', '');
        $totalInterventions = $interventionRepository->count([]);
        $totalUsers = $userRepository->count([]);
        $totalTechniciens = $userRepository->countByRole('ROLE_TECHNICIEN');
        $search = $request->query->get('search', '');
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        
        $allIntervenants = $userRepository->findAll(); // ou tous les users

        if (in_array('ROLE_ADMIN', $user->getRoles()) && $view === 'all') {
            $interventions = $interventionRepository->findByFilters($view === 'mine' ? $user : null,
            $search,
            $start,
            $end,
            $intervenant);
        } else {
            $interventions = $interventionRepository->findByFilters($view === 'mine' ? $user : null,
            $search,
            $start,
            $end,
            $intervenant);
        }


        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'interventions' => $interventions,
            'view' => $view,
            'search' => $search,
            'start' => $start,
            'end' => $end,
            'intervenant' => $intervenant,
            'allIntervenants' => $allIntervenants,
            'totalInterventions' => $totalInterventions,
        ]);
    }



    #[Route('/profil/edit-photo', name: 'profil_edit_photo')]
    public function editPhoto(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('photo', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Nouvelle photo de profil',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $photoFile = $form->get('photo')->getData();

        //     if ($photoFile) {
        //         $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
        //         $safeFilename = $slugger->slug($originalFilename);
        //         $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

        //         try {
        //             $photoFile->move(
        //                 $this->getParameter('photo_directory'),
        //                 $newFilename
        //             );

        //             $user->setPhoto($newFilename);
        //             $entityManager->persist($user);
        //             $entityManager->flush();

        //             $this->addFlash('success', 'Photo de profil mise à jour avec succès !');
        //             return $this->redirectToRoute('app_profil');
        //         } catch (\Exception $e) {
        //             $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
        //         }
        //     }
        // }
        if ($form->isSubmitted() && $form->isValid()) {
        
            // Gérer upload photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $filename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($this->getParameter('photo_directory'), $filename);
                $user->setPhoto($filename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour.');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('user/edit_photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/export/pdf', name: 'export_interventions_pdf')]
    public function exportPdf(
        Request $request,
        InterventionRepository $interventionRepository,
        UserInterface $user
    ): Response {
        $view = $request->query->get('view', 'mine');
        $search = $request->query->get('search', '');
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        if (in_array('ROLE_ADMIN', $user->getRoles()) && $view === 'all') {
            $interventions = $interventionRepository->findByFilters(null, $search, $start, $end);
        } else {
            $interventions = $interventionRepository->findByFilters($user, $search, $start, $end);
        }

        // Préparer HTML pour le PDF
        $html = $this->renderView('profil/intervention_export_pdf.html.twig', [
            'interventions' => $interventions,
        ]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="interventions.pdf"'
            ]
        );
    }
    #[Route('/profil/export/excel', name: 'export_interventions_excel')]
    public function exportExcel(
        Request $request,
        InterventionRepository $interventionRepository,
        UserInterface $user
    ): Response {
        $view = $request->query->get('view', 'mine');
        $search = $request->query->get('search', '');
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        if (in_array('ROLE_ADMIN', $user->getRoles()) && $view === 'all') {
            $interventions = $interventionRepository->findByFilters(null, $search, $start, $end);
        } else {
            $interventions = $interventionRepository->findByFilters($user, $search, $start, $end);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Interventions");

        // En-têtes
        $sheet->fromArray([
            'ID', 'Intervenant', 'Code Barre', 'Modèle', 'Date', 'Commentaire'
        ], null, 'A1');

        // Données
        $row = 2;
        foreach ($interventions as $intervention) {
            $sheet->setCellValue("A$row", $intervention->getId());
            $sheet->setCellValue("B$row", $intervention->getIntervenant()->getPrenom() . ' ' . $intervention->getIntervenant()->getNomUser());
            $sheet->setCellValue("C$row", $intervention->getCodeBarre());
            $sheet->setCellValue("D$row", $intervention->getModele());
            $sheet->setCellValue("E$row", $intervention->getDateIntervention()?->format('d/m/Y'));
            $sheet->setCellValue("F$row", $intervention->getCommentaire());
            $row++;
        }

        // Préparation de la réponse
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $filename = 'interventions_export.xlsx';
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }


}