<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\ApiClients;
use App\Entity\ApiClientsGrants;
use App\Entity\ApiInstallPerm;
use App\Entity\Franchise;
use App\Entity\Structure;
use App\Form\ApiClientsGrantsType;
use App\Form\ApiInstallPermType;
use App\Form\FranchiseEditType;
use App\Form\FranchiseType;
use App\Form\StructureType;
use App\Repository\AdminRepository;
use App\Repository\ApiClientsGrantsRepository;
use App\Repository\ApiClientsRepository;
use App\Repository\ApiInstallPermRepository;
use App\Repository\FranchiseRepository;
use App\Repository\StructureRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
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

    #[Route('/dashboard/admin', name: 'app_dashboard_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', ['data' => '']);
    }

    #[Route('/dashboard/admin/franchise/new', name: 'app_dashboard_admin_new_franchise', methods: ['GET', 'POST'])]
    public function new_franchise(Request $request, JWTService $jWTService, SendMailService $sendMailService, FranchiseRepository $franchiseRepository, ApiClientsRepository $apiClientsRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $franchise = new Franchise();
        $form = $this->createForm(FranchiseType::class, $franchise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $franchise->getClient()->setClientId(""); //Temporary not nullable
            $franchiseRepository->add($franchise, true);

            // JWT Token generation
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $franchise->getId()
            ];

            $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $franchise->getClient()->setClientId($token);
            $entityManager->persist($franchise);
            $entityManager->flush();

            //TODO: send Twig mail w/ link button on POST route 127.0.0.1:8000/verif/{token}
            //for franchise account activation
            /*$sendMailService->send(
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
    public function show_franchise(Request $request, FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);
        $structures = $structureRepository->findDetails($franchise);

        $perm = new ApiInstallPerm();
        $form = $this->createForm(ApiInstallPermType::class, $perm);
        $form->handleRequest($request);

        return $this->renderForm('admin/details-franchise.html.twig', [
            'franchise' => $franchise,
            'structures' => $structures,
            'form' => $form
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

    #[Route('/dashboard/admin/franchise/{id}/structure/new', name: 'app_dashboard_admin_new_structure', methods: ['GET', 'POST'])]
    public function new_structure(Request $request, JWTService $jWTService, SendMailService $sendMailService, FranchiseRepository $franchiseRepository, StructureRepository $structureRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);

        $structure = new Structure();
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $structure->getClient()->setClientId(""); //Temporary not nullable
            $structureRepository->add($structure, true);

            // JWT Token generation
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $structure->getId()
            ];

            $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));
            $structure->getClient()->setClientId($token);

            // Set the franchise
            $structure->setFranchise($franchise);

            $entityManager->persist($structure);
            $entityManager->flush();

            //TODO: send Twig mail w/ link button on POST route 127.0.0.1:8000/verif/{token}
            //for structure account activation
            /*$sendMailService->send(
                'no-reply@fj-fitness.fr',
                $franchise->getEmail(),
                'Activation de votre compte sur le site FJ Fitness',
                'register',
                compact('franchise', 'token')
            );*/

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
            $result['franchises']['error'] = "Aucun r??sultat";
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

        $franchises = $franchiseRepository->getActiveFranchises($domain);

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

        $franchises = $franchiseRepository->getInactiveFranchises($domain);

        if ($domain instanceof Admin) {
            return $this->render('admin/franchises-list.html.twig', [
                'franchises' => $franchises
            ]);
        }
    }

    #[Route('/dashboard/admin/franchise/{id}', name: 'app_dashboard_admin_franchise_setActive')]
    public function active_franchise(FranchiseRepository $franchiseRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $franchise = $franchiseRepository->find($id);

        $franchise->setIsActive(!$franchise->isActive());
        $franchise->getClient()->setActive($franchise->isActive() ? '1' : '0');
        $entityManager->persist($franchise);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard_admin_franchise_details', ['id' => $id]);
    }

    #[Route('/dashboard/admin/franchise/{id}/structure/{structure_id}', name: 'app_dashboard_admin_structure_setActive', requirements: ['id' => '\d+'])]
    public function active_structure(StructureRepository $structureRepository, EntityManagerInterface $entityManager, int $id, int $structure_id): Response
    {
        $structure = $structureRepository->find($structure_id);

        $structure->setIsActive(!$structure->isActive());
        $structure->getClient()->setActive($structure->isActive() ? '1' : '0');
        $entityManager->persist($structure);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard_admin_franchise_details', ['id' => $id]);
    }
}
