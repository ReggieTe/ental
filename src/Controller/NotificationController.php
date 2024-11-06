<?php

namespace App\Controller;

use App\Entity\AppUserNotification;
use App\Entity\UserAdmin;
use App\Entity\UserAppNotification;
use App\Service\ApiService;
use App\Service\DashboardService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class NotificationController extends AbstractController
{
    private $dashboardServicePermissions;
    private $doctrine;
    private $apiService;

    public function __construct(ManagerRegistry $doctrine, DashboardService $dashboardServicePermissions,
        ApiService $apiService) {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->apiService = $apiService;
    }

    #[Route("dashboard/notifications", name: "Notifications")]

    public function index(#[CurrentUser] UserAdmin $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $notifications = [];
        foreach ($user->getNotifications() as $value) {
            $state = $this->apiService->getNotificationState($value);
            $notification = $this->apiService->sortObjectToArray($value);
            $notification['read'] = $state;
            array_push($notifications, $notification);
        }

        return $this->render('dashboard/notification/index.html.twig', [
            'notifications' => $notifications,
            'title' => 'Notifications',
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

    #[Route("/dashboard/notification/delete/{id}", name: "Delete Notification", methods: ["GET", "DELETE"])]
    public function delete(#[CurrentUser] UserAdmin $user, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //notification status
        $objectToDelete = $this->doctrine->getRepository(UserAppNotification::class)->findOneBy(['appNotification' => $id]);
        //notification
        $notification = $this->doctrine->getRepository(AppUserNotification::class)->find($id);
        if ($objectToDelete && $notification) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($objectToDelete);
            $entityManager->remove($notification);
            $entityManager->flush();
            $this->addFlash('success', "Notification deleted successfully");
        } else {
            $this->addFlash('danger', "Failed to delete notification");
        }
        return $this->redirectToRoute('Notifications');
    }

    #[Route("dashboard/notification/{id}", name: "Read Notification")]

    public function read(#[CurrentUser] UserAdmin $user, Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $notification = $this->doctrine->getRepository(UserAppNotification::class)->findOneBy(['appNotification' => $id]);

        if ($notification) {
            if (!$notification->getNotificationRead()) {
                $entityManager = $this->doctrine->getManager();
                $notification->setNotificationRead(true);
                $entityManager->persist($notification);
                $entityManager->flush();
            }
        }
        //Get actuall notification
        $notification = $this->doctrine->getRepository(AppUserNotification::class)->find($id);
        $notification = $this->apiService->sortObjectToArray($notification);

        return $this->render('dashboard/notification/read/index.html.twig', [
            'notification' => $notification,
            'title' => 'Notification',
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

}
