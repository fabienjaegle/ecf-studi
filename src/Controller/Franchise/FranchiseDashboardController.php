<?php

namespace App\Controller\Franchise;

use App\Entity\Franchise;
use App\Form\FranchiseEditType;
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
    #[Route('/dashboard/franchise/index', name: 'app_dashboard_franchise_index')]
    public function index(FranchiseRepository $franchiseRepository): Response
    {
        return $this->render('franchise/index.html.twig', [
            'franchises' => $franchiseRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/franchise/new', name: 'app_dashboard_new_franchise', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_dashboard_franchise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('franchise/new.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/franchise/{id}', name: 'app_dashboard_franchise_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(FranchiseRepository $franchiseRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);

        return $this->render('franchise/details.html.twig', [
            'franchise' => $franchise,
        ]);
    }

    #[Route('/dashboard/franchise/{id}/edit', name: 'app_dashboard_edit_franchise', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, int $id, FranchiseRepository $franchiseRepository): Response
    {
        $franchise = $franchiseRepository->find($id);

        $form = $this->createForm(FranchiseEditType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $franchiseRepository->add($franchise, true);

            return $this->redirectToRoute('app_dashboard_franchise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('franchise/edit.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/franchise/{id}', name: 'app_dashboard_delete_franchise', methods: ['POST'])]
    public function delete(Request $request, Franchise $franchise, FranchiseRepository $franchiseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $franchise->getId(), $request->request->get('_token'))) {
            $franchiseRepository->remove($franchise, true);
        }

        return $this->redirectToRoute('app_dashboard_franchise_index', [], Response::HTTP_SEE_OTHER);
    }
}
