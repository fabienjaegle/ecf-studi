<?php

namespace App\Controller\Admin;

use App\Repository\FranchiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard/admin', name: 'app_dashboard_admin_index')]
    public function index(FranchiseRepository $franchiseRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'franchises' => $franchiseRepository->findAll(),
        ]);
    }
}
