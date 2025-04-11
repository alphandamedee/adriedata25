<?php

namespace App\Controller;

use App\Repository\InterventionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    // #[Route('/user/dashboard', name: 'user_dashboard')]
    // public function dashboard(UserRepository $userRepository): Response
    // {
    //     $users = [];

    //     // Seulement si l'utilisateur est admin
    //     if ($this->isGranted('ROLE_ADMIN')) {
    //         $users = $userRepository->findAll();
    //     }

    //     return $this->render('user/dashboard.html.twig', [
    //         'users' => $users,
    //     ]);
    // }

    #[Route('/api/dashboard-data', name: 'dashboard_data', methods: ['GET'])]
    public function getDashboardData(Request $request, InterventionRepository $interventionRepository): JsonResponse
    {
        $date = $request->query->get('date', (new \DateTime())->format('Y-m-d'));
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);

        // Fetch interventions and products data based on the selected date
        $interventions = $interventionRepository->findByDate($dateTime);

        $interventionsCount = count($interventions);
        $productsCount = count(array_unique(array_map(function ($intervention) {
            return $intervention->getProduit()->getId();
        }, $interventions)));

        // Prepare data for charts
        $labels = [];
        $interventionsData = [];
        $productsData = [];

        foreach ($interventions as $intervention) {
            $labels[] = $intervention->getDateIntervention()->format('H:i');
            $interventionsData[] = 1; // Each intervention counts as 1
            $productsData[] = 1; // Each product counts as 1
        }

        return new JsonResponse([
            'interventionsCount' => $interventionsCount,
            'productsCount' => $productsCount,
            'labels' => $labels,
            'interventionsData' => $interventionsData,
            'productsData' => $productsData,
        ]);
    }
}
