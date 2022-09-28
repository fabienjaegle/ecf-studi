<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard/admin', name: 'app_dashboard_admin_index')]
    public function index(): Response
    {
        return $this->render('admin.html.twig', []);
    }
}
