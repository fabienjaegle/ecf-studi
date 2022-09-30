<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\ApiClients;
use App\Entity\ApiClientsGrants;
use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\FranchiseEditType;
use App\Form\FranchiseType;
use App\Form\StructureType;
use App\Repository\ApiClientsRepository;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminDashboardController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/dashboard/admin/franchise/new', name: 'app_dashboard_admin_new_franchise', methods: ['GET', 'POST'])]
    public function new_franchise(Request $request, SendMailService $mail, JWTService $jwt, FranchiseRepository $franchiseRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

            if ($this->security->getUser() instanceof Admin) {
                // Set the domain in case of the user instance.
                $franchise->setDomain($this->security->getUser());
            }

            $entityManager->persist($franchise);
            $entityManager->flush();

            // On génère le JWT de l'utilisateur
            // On crée le Header
            /*$header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $franchise->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@fj-fitness.fr',
                $franchise->getEmail(),
                'Activation de votre compte sur le site FJ Fitness',
                'register',
                compact('franchise', 'token')
            );*/

            return $this->redirectToRoute('app_dashboard_admin_inactive_franchises', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new-franchise.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/admin/franchises/active', name: 'app_dashboard_admin_active_franchises')]
    public function active_franchises(): Response
    {
        $domain = $this->security->getUser();

        if ($domain instanceof Admin) {
            return $this->render('admin/active-franchises.html.twig', [
                'active_franchises' => $domain->getFranchises()->filter(function ($item) {
                    return $item->isActive();
                })->getValues()
            ]);
        }
    }

    #[Route('/dashboard/admin/franchises/inactive', name: 'app_dashboard_admin_inactive_franchises')]
    public function inactive_franchises(): Response
    {
        $domain = $this->security->getUser();

        if ($domain instanceof Admin) {
            return $this->render('admin/inactive-franchises.html.twig', [
                'inactive_franchises' => $domain->getFranchises()->filter(function ($item) {
                    return !$item->isActive();
                })->getValues(),
            ]);
        }
    }

    #[Route('/dashboard/admin/franchise/{id}', name: 'app_dashboard_admin_franchise_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(FranchiseRepository $franchiseRepository, ApiClientsRepository $apiClientsRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);
        $client = $apiClientsRepository->findOneBy(['client_id' => $id]);

        return $this->render('admin/details-franchise.html.twig', [
            'franchise' => $franchise,
            'client' => $client
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}/edit', name: 'app_dashboard_admin_edit_franchise', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, int $id, FranchiseRepository $franchiseRepository): Response
    {
        $franchise = $franchiseRepository->find($id);

        $form = $this->createForm(FranchiseEditType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $franchiseRepository->add($franchise, true);

            return $this->redirectToRoute('app_dashboard_admin_active_franchises', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit-franchise.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/franchise/{id}', name: 'app_dashboard_admin_delete_franchise', methods: ['POST'])]
    public function delete(Request $request, Franchise $franchise, FranchiseRepository $franchiseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $franchise->getId(), $request->request->get('_token'))) {
            $franchiseRepository->remove($franchise, true);
        }

        return $this->redirectToRoute('app_dashboard_franchise_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/dashboard/admin/franchise/{id}/structure/new', name: 'app_dashboard_admin_new_structure', methods: ['GET', 'POST'])]
    public function new_structure(Request $request, FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);

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

            // Set the franchise
            $structure->setFranchise($franchise);

            $entityManager->persist($structure);
            $entityManager->flush();

            $apiClient = new ApiClients();
            $apiClient->setClientId($id);
            $apiClient->setClientSecret($structure->getPassword());
            $apiClient->setClientName($structure->getName());
            $apiClient->setActive("0");
            $apiClient->setShortDescription("");
            $apiClient->setFullDescription("");
            $apiClient->setLogoUrl("");
            $apiClient->setUrl("");
            $apiClient->setDpo("");
            $apiClient->setTechnicalContact("");
            $apiClient->setCommercialContact("");

            $entityManager->persist($apiClient);
            $entityManager->flush();

            $apiClientGrants = new ApiClientsGrants();
            $apiClientGrants->setClient($apiClient);
            $apiClientGrants->setInstallId($franchise->getDomain()->getId());
            $apiClientGrants->setActive("0");
            $apiClientGrants->setPerms("");
            $apiClientGrants->setBranchId($structure->getId());

            $entityManager->persist($apiClientGrants);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_admin_franchise_details', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new-structure.html.twig', [
            'franchise_id' => $id,
            'structure' => $structure,
            'form' => $form,
        ]);
    }
}
