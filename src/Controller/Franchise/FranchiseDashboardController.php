<?php

namespace App\Controller\Franchise;

use App\Entity\Franchise;
use App\Form\FranchiseType;
use App\Repository\FranchiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class FranchiseDashboardController extends AbstractController
{
    #[Route('/dashboard/admin/franchise/index', name: 'app_dashboard_admin_franchise_index')]
    public function index(FranchiseRepository $franchiseRepository): Response
    {
        return $this->render('franchise/index.html.twig', [
            'franchises' => $franchiseRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/admin/franchise/new', name: 'app_dashboard_admin_new_franchise', methods: ['GET', 'POST'])]
    public function new(Request $request, FranchiseRepository $franchiseRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $franchise = new Franchise();
        $form = $this->createForm(FranchiseType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $franchiseRepository->add($franchise, true);

            // encode the password
            $franchise->setPassword(
                $userPasswordHasher->hashPassword(
                    $franchise,
                    $form->get('password')->getData()
                )
            );

            // Set the role
            $franchise->setRoles(['ROLE_FRANCHISE']);
            $entityManager->persist($franchise);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('franchise/new-franchise.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }
}
