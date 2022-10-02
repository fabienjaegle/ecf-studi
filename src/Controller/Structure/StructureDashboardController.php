<?php

namespace App\Controller\Structure;

use App\Entity\Structure;
use App\Form\StructureEditType;
use App\Repository\StructureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
