<?php

namespace App\Controller;

use App\Entity\UserAdmin;
use App\Entity\UserCarAdditional;
use App\Form\CarAdditionalType;
use App\Service\ApiService;
use App\Service\DashboardService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CarAdditionalController extends AbstractController
{
    private $apiService;
    private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(
        ApiService $apiService,
        ManagerRegistry $doctrine, DashboardService $dashboardServicePermissions) {
        $this->apiService = $apiService;
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
    }
    #[Route("dashboard/additionals", name: "View Additionals")]

    public function index(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('dashboard/cars/additionals/index.html.twig', [
            'title' => "Additional instructions",
            'list' => $this->apiService->process($user->getUserCarAdditionals()),
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

    #[Route("dashboard/additional/add/{id?}", name: "Add or Edit Additionals", methods: ["GET", "POST"])]

    public function add(#[CurrentUser] UserAdmin $user, Request $request, ?string $id = ''): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = new UserCarAdditional();

        $formTitle = 'Add additional instructions';

        if (!empty($id)) {
            $formTitle = "Update additional instructions";
            $instance = $this->doctrine->getRepository(UserCarAdditional::class)->find($id);
        }
        $form = $this->createForm(CarAdditionalType::class, $instance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setOwner($user);
            $entityManager->persist($instance);
            $entityManager->flush();
            $this->addFlash('success', $formTitle . ' was successfully');
            $form = $this->createForm(CarAdditionalType::class, $instance);
            $id = $instance->getId();
        }

        return $this->render('dashboard/cars/additionals/add/index.html.twig', [
            'form' => $form,
            'id' => $id,
            'title' => $formTitle,
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

    #[Route("/dashboard/additional/delete/{id?}", name: "Delete Additional", methods: ["GET", "POST"])]

    public function delete(#[CurrentUser] UserAdmin $user, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $item = $this->doctrine->getRepository(UserCarAdditional::class)->find($id);
        if ($item) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
        } else {
            $this->addFlash('danger', "Failed to delete item");
        }
        return $this->redirect("/dashboard/additionals");
    }

}
