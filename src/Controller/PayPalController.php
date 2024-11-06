<?php

namespace App\Controller;

use App\Entity\UserAdmin;
use App\Entity\UserPaypal;
use App\Form\PayPalType;
use App\Service\DashboardService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PayPalController extends AbstractController
{

    private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine, DashboardService $dashboardServicePermissions) {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
    }
    #[Route("/dashboard/profile/paypal", name: "User Paypal")]
    public function index(#[CurrentUser] UserAdmin $user, Request $request): Response
    {

        $instance = $user->getUserPayPal();
        $formTitle = 'Add Paypal Account';

        if (!$instance) {
            $instance = new UserPayPal();
            $formTitle = "Update  Paypal Account";
        }
        $form = $this->createForm(PayPalType::class, $instance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setClient($user);
            $entityManager->persist($instance);
            $entityManager->flush();
            $this->addFlash('success', $formTitle . ' was successfully');
            $form = $this->createForm(PayPalType::class, $instance);
            $id = $instance->getId();
        }

        return $this->render('dashboard/paypal/add/index.html.twig', [
            'form' => $form,
            'title' => $formTitle,
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);

    }

    #[Route("/dashboard/profile/paypal/delete/{id}", name: "Delete Paypal account", methods: ["GET", "POST"])]
    public function delete(#[CurrentUser] UserAdmin $user, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $item = $this->doctrine->getRepository(UserPayPal::class)->find($id);
        if ($item) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
        } else {
            $this->addFlash('danger', "Failed to delete item");
        }
        return $this->redirect("/dashboard/profile/paypal");
    }

}
