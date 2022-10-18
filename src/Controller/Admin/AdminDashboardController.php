<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\ApiClients;
use App\Entity\ApiClientsGrants;
use App\Entity\ApiInstallPerm;
use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\ApiClientsGrantsType;
use App\Form\FranchiseEditType;
use App\Form\FranchiseType;
use App\Form\StructureType;
use App\Repository\AdminRepository;
use App\Repository\ApiClientsGrantsRepository;
use App\Repository\ApiClientsRepository;
use App\Repository\ApiInstallPermRepository;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', ['data' => '']);
    }

    #[Route('/dashboard/admin/franchise/new', name: 'app_dashboard_admin_new_franchise', methods: ['GET', 'POST'])]
    public function new_franchise(Request $request, FranchiseRepository $franchiseRepository, ApiClientsRepository $apiClientsRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $franchise = new Franchise();
        $form = $this->createForm(FranchiseType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $franchise->getClient()->setClientId($this->Guid());
            $franchise->getClient()->setClientName($franchise->getName());
            $franchise->getClient()->setActive("0");

            // encode the password
            $franchise->setPassword(
                $userPasswordHasher->hashPassword(
                    $franchise,
                    $form->get('password')->getData()
                )
            );

            // Hashed client secret
            $franchise->getClient()->setClientSecret($franchise->getPassword());

            // Set the role
            $franchise->setRoles(['ROLE_FRANCHISE']);

            if ($this->security->getUser() instanceof Admin) {
                // Set the domain in case of the user instance.
                $franchise->setDomain($this->security->getUser());
            }

            $franchiseRepository->add($franchise, true);

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

            return $this->redirectToRoute('app_dashboard_admin_franchises_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new-franchise.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/admin/franchises/list', name: 'app_dashboard_admin_franchises_list')]
    public function franchises_list(): Response
    {
        $domain = $this->security->getUser();

        if ($domain instanceof Admin) {
            return $this->render('admin/franchises-list.html.twig', [
                'franchises' => $domain->getFranchises()
            ]);
        }
    }

    #[Route('/dashboard/admin/franchise/{id}', name: 'app_dashboard_admin_franchise_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show_franchise(FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);
        $structures = $structureRepository->findDetails($franchise);

        return $this->render('admin/details-franchise.html.twig', [
            'franchise' => $franchise,
            'structures' => $structures
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}/active', name: 'app_dashboard_admin_filter_active_structures', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show_active_franchise(FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);
        $structures = $structureRepository->findDetails($franchise, true);

        return $this->render('admin/details-franchise.html.twig', [
            'franchise' => $franchise,
            'structures' => $structures
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}/inactive', name: 'app_dashboard_admin_filter_inactive_structures', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show_inactive_franchise(FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);
        $structures = $structureRepository->findDetails($franchise, false);

        return $this->render('admin/details-franchise.html.twig', [
            'franchise' => $franchise,
            'structures' => $structures
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}/edit', name: 'app_dashboard_admin_edit_franchise', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit_franchise(Request $request, int $id, FranchiseRepository $franchiseRepository): Response
    {
        $franchise = $franchiseRepository->find($id);

        $form = $this->createForm(FranchiseEditType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $franchiseRepository->add($franchise, true);


            return $this->redirectToRoute('app_dashboard_admin_franchises_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit-franchise.html.twig', [
            'franchise' => $franchise,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}', name: 'app_dashboard_admin_delete_franchise', methods: ['POST'])]
    public function delete_franchise(Request $request, Franchise $franchise, FranchiseRepository $franchiseRepository): Response
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
            $structure->getClient()->setClientId($this->Guid());
            $structure->getClient()->setClientName($structure->getName());
            $structure->getClient()->setActive("0");

            // Set the role
            $structure->setRoles(['ROLE_STRUCTURE']);

            // encode the password
            $structure->setPassword(
                $userPasswordHasher->hashPassword(
                    $structure,
                    $form->get('password')->getData()
                )
            );

            // Hashed client secret
            $structure->getClient()->setClientSecret($structure->getPassword());

            $structureRepository->add($structure, true);

            // Set the franchise
            $structure->setFranchise($franchise);

            $entityManager->persist($structure);
            $entityManager->flush();

            /*$apiInstallPerms = new ApiInstallPerm();
            $apiInstallPerms->setBranchId($structure->getId());
            $apiInstallPerms->setInstallId($franchise->getDomain()->getId());
            $apiInstallPerms->setClientGrants($apiClientGrants);
            $apiInstallPerms->setMembersAdd(true);
            $apiInstallPerms->setMembersRead(true);
            $apiInstallPerms->setMembersWrite(true);
            $apiInstallPerms->setMembersPaymentSchedulesRead(true);
            $apiInstallPerms->setMembersProductsAdd(true);
            $apiInstallPerms->setMembersStatistiquesRead(true);
            $apiInstallPerms->setMembersSubscriptionRead(true);
            $apiInstallPerms->setPaymentDayRead(true);
            $apiInstallPerms->setPaymentSchedulesRead(true);
            $apiInstallPerms->setPaymentSchedulesWrite(true);

            $entityManager->persist($apiInstallPerms);
            $entityManager->flush();*/

            return $this->redirectToRoute('app_dashboard_admin_franchise_details', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new-structure.html.twig', [
            'franchise_id' => $id,
            'structure' => $structure,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/admin/franchise/{id}/structure/{structure_id}', name: 'app_dashboard_admin_structure_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show_structure(StructureRepository $structureRepository, ApiClientsGrantsRepository $apiClientsGrantsRepository, int $id, int $structure_id): Response
    {
        $structure = $structureRepository->find($structure_id);

        return $this->render('admin/details-structure.html.twig', [
            'franchise_id' => $id,
            'structure' => $structure
        ]);
    }

    #[Route('/dashboard/admin/search', name: 'app_dashboard_admin_search', methods: ['POST'])]
    public function searchAction(FranchiseRepository $franchiseRepository, Request $request)
    {
        $result = $franchiseRepository->findByNameField(
            $request->query->get('searchValue')
        );

        var_dump($result);
        die();

        if (!$result) {
            $result['franchises']['error'] = "Aucun résultat";
        } else {
            $result['franchises'] = $this->getRealEntities($result);
        }

        //return new Response(json_encode($result));

        return $this->render('admin/franchises-list.html.twig', ['franchises' => $result['franchises']]);
    }

    public function getRealEntities($entities)
    {

        foreach ($entities as $entity) {
            $realEntities[$entity->getId()] = array(
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'address' => $entity->getAddress(),
                'zipcode' => $entity->getZipCode(),
                'city' => $entity->getCity(),
                'email' => $entity->getEmail(),
                'active' => $entity->isActive() ? 'Oui' : 'Non'
            );
        }

        return $realEntities;
    }

    #[Route('/dashboard/admin/structure/{id}/add/permissions/{client_id}', name: 'app_dashboard_admin_structure_add_permissions', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function add_permissions(Request $request, ApiClientsRepository $apiClientsRepository, ApiClientsGrantsRepository $apiClientsGrantsRepository, EntityManagerInterface $entityManager, int $id, int $client_id): Response
    {
        $apiClients = $apiClientsRepository->find($client_id);

        $apiClientsGrants = new ApiClientsGrants();
        $form = $this->createForm(ApiClientsGrantsType::class, $apiClientsGrants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $apiClientsGrantsRepository->add($apiClientsGrants, true);

            $apiClientsGrants->setClient($apiClients->getClientId());

            $entityManager->persist($apiClients);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_admin_franchise_details', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/add-permissions.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/dashboard/admin/franchises/list/active', name: 'app_dashboard_admin_filter_active_franchises', methods: ['GET'])]
    public function filter_active(FranchiseRepository $franchiseRepository): Response
    {
        $domain = $this->security->getUser();

        $franchises = $franchiseRepository->getFranchises($domain, true);

        if ($domain instanceof Admin) {
            return $this->render('admin/franchises-list.html.twig', [
                'franchises' => $franchises
            ]);
        }
    }

    #[Route('/dashboard/admin/franchises/list/inactive', name: 'app_dashboard_admin_filter_inactive_franchises', methods: ['GET'])]
    public function filter_inactive(FranchiseRepository $franchiseRepository): Response
    {
        $domain = $this->security->getUser();

        $franchises = $franchiseRepository->getFranchises($domain, false);

        if ($domain instanceof Admin) {
            return $this->render('admin/franchises-list.html.twig', [
                'franchises' => $franchises
            ]);
        }
    }
}
