<?php

namespace App\Controller\Api;

use App\Entity\UserFCMToken;
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
class FcmTokenApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }

    #[Route("/v1/save/fcm", name: "fcm_save", methods: ["POST"])]
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
    #[OA\Tag(name: 'FcmToken')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id') != null ? $request->get('id') : null;
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            if (empty($user->getFcmId())) {
                $user->setFcmId($id);
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $message = 'Token saved successfully';
            }{
                $message = 'Token saved already';
            }
            return $this->respond(["message" => $message, 'token' => $id]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/delete/fcm", name: "fcm_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'FcmToken')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {
        try {
            $token = $request->get('id') != null ? $request->get('id') : null;
            if ($token == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $entityManager = $this->doctrine->getManager();
            $objectToDelete = $this->doctrine->getRepository(UserFCMToken::class)->findBy(['token' => $token]);
            foreach ($objectToDelete as $delete) {
                $entityManager->remove($delete);
            }
            $entityManager->flush();
            return $this->respond(['message' => "Item deleted successfully"]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
