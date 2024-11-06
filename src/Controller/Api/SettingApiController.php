<?php

namespace App\Controller\Api;

use App\Service\ApiService;
use App\Service\Validate;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'api_')]
class SettingApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;

public function __construct(ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
        $this->doctrine = $doctrine;
    }

        #[Route("/v1/settings", name: "settings", methods: ["POST"])]
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
    #[OA\Tag(name: 'Setting')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $data = Validate::parameter($request->get('data'));

            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $settings = $user->getUserSetting();
            switch ($id) {
                case "notifications":
                    $settings->setNotifications($data);
                    break;
                case "email":
                    $settings->setEmail($data);
                    break;
                case "sms":
                    $settings->setSms($data);
                    break;
                case "sms":
                    $settings->setAccount($data);
                    break;
                default:
                    break;
            }

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($settings);
            $entityManager->flush();
            return $this->respond(["message" => 'Settings updated successfully', "setting" => $id, "state" => $data]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }
}
