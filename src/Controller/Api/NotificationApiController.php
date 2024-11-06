<?php

namespace App\Controller\Api;

use App\Entity\UserAppNotification;
use App\Service\ApiService;
use App\Service\Validate;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class NotificationApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }

    #[Route("/v1/notification", name: "notifications", methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Success message '
    )]
    #[OA\Parameter(
        name: 'apiKey',
        in: 'query',
        description: 'App Secret key',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'token',
        in: 'query',
        description: 'Authentication token',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Notification')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $notifications = [];
            foreach ($user->getNotifications() as $value) {
                $state = $this->apiService->getNotificationState($value);
                $notification = $this->apiService->sortObjectToArray($value);
                $notification['read'] = $state;
                array_push($notifications, $notification);
            }
            return $this->respond([
                'notifications' => $notifications,
            ]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/notification/update", name: "notification_update", methods: ["POST"])]
    #[OA\Response(
        response: 200,
        description: 'Success message '
    )]
    #[OA\Parameter(
        name: 'apiKey',
        in: 'query',
        description: 'App Secret key',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'token',
        in: 'query',
        description: 'Authentication token',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Notification')]
    #[Security(name: 'Token')]
    public function read(Request $request): JsonResponse
    {
        try {
            $id = Validate::parameter($request->get('id'));
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };

            $notification = $this->doctrine->getRepository(UserAppNotification::class)->findOneBy(['appNotification' => $id]);
            if (!$notification->getNotificationRead()) {
                $entityManager = $this->doctrine->getManager();
                $notification->setNotificationRead(true);
                $entityManager->persist($notification);
                $entityManager->flush();
                return $this->respond([
                    'message' => 'Notification state updated',
                ]);
            }
            return $this->respondError(["message" => "Already updated"]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/notification/delete", name: "notification_delete", methods: ["DELETE"])]
    #[OA\Response(
        response: 200,
        description: 'Success message '
    )]
    #[OA\Parameter(
        name: 'apiKey',
        in: 'query',
        description: 'App Secret key',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'token',
        in: 'query',
        description: 'Authentication token',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Item Id',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Notification')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {
        try {
            $id = Validate::parameter($request->get('id'));
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            if ($id) {
                $objectToDelete = $this->doctrine->getRepository(UserAppNotification::class)->findOneBy(['appNotification' => $id]);
                if ($objectToDelete) {
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($objectToDelete);
                    $entityManager->flush();
                    return $this->respond(["message" => "Item deleted successfully"]);
                }
            }
            return $this->respondError(["message" => "invalid id " . $id]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
