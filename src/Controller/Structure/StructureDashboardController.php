<?php

namespace App\Controller\Structure;

use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\StructureEditType;
use App\Form\StructureType;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StructureDashboardController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/dashboard/structure/index', name: 'app_dashboard_structure_index')]
    public function index(StructureRepository $structureRepository): Response
    {
        return $this->render('structure/index.html.twig', [
            'structures' => $structureRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/structure/new', name: 'app_dashboard_new_structure', methods: ['GET', 'POST'])]
    public function new(Request $request, StructureRepository $structureRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $structure = new Structure();
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureRepository->add($structure, true);

            // encode the password
            $structure->setPassword(
                $userPasswordHasher->hashPassword(
                    $structure,
                    $form->get('password')->getData()
                )
            );

            // Set the role
            $structure->setRoles(['ROLE_STRUCTURE']);

            if ($this->security->getUser() instanceof Franchise) {
                // Set the franchise in case of the user instance.
                $structure->setFranchise($this->security->getUser());
            }

            $entityManager->persist($structure);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_structure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('structure/new.html.twig', [
            'structure' => $structure,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/structure/{id}', name: 'app_dashboard_structure_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(StructureRepository $structureRepository, int $id): Response
    {
        $structure = $structureRepository->find($id);

        return $this->render('structure/details.html.twig', [
            'structure' => $structure,
        ]);
    }

    #[Route('/dashboard/structure/{id}/edit', name: 'app_dashboard_edit_structure', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, int $id, StructureRepository $structureRepository): Response
    {
        $structure = $structureRepository->find($id);

        $form = $this->createForm(StructureEditType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureRepository->add($structure, true);

            return $this->redirectToRoute('app_dashboard_structure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('structure/edit.html.twig', [
            'structure' => $structure,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/structure/{id}', name: 'app_dashboard_delete_structure', methods: ['POST'])]
    public function delete(Request $request, Structure $structure, StructureRepository $structureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $structure->getId(), $request->request->get('_token'))) {
            $structureRepository->remove($structure, true);
        }

        return $this->redirectToRoute('app_dashboard_structure_index', [], Response::HTTP_SEE_OTHER);
    }
}
