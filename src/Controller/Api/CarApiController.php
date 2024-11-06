<?php

namespace App\Controller\Api;

use App\Entity\Car;
use App\Form\Api\CarType;
use App\Service\ApiService;
use App\Service\Common;
use App\Service\Validate;
use App\Util\Form;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class CarApiController extends AbstractApiController
{
    private $common;
    private $apiService;
    private $doctrine;

    public function __construct(
        Common $common,
        ManagerRegistry $doctrine, ApiService $apiService) {
        $this->common = $common;
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }
    #[Route("/v1/cars", name: "cars", methods: ["GET"])]
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
    #[OA\Tag(name: 'Car')]
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
            return $this->respond([
                'list' => $this->apiService->process($user->getCars()),
            ]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/car/add", name: "car_add", methods: ["POST"])]

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
    #[OA\Tag(name: 'Car')]
    #[Security(name: 'Token')]
    public function add(Request $request): JsonResponse
    {
        try {
            $instance = new Car();
            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $formTitle = 'Add car';
            $images = [];

            if (!empty($id)) {
                $formTitle = "Update car";
                $instance = $this->doctrine->getRepository(Car::class)->find($id);
                $images = $this->common->getFiles($id, $user->getId(), 'car');
            }
            $form = $this->buildForm(CarType::class, $instance);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->doctrine->getManager();
                $instance->setOwner($user);
                $entityManager->persist($instance);
                $entityManager->flush();
                return $this->respond([
                    "message" => $formTitle . ' was successfully',
                    'id' => $instance->getId(),
                ]);
            }
            return $this->respondError([
                'message' => "Form contains errors",
                'errors' => Form::ErrorMessages($form),
            ]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/car/delete", name: "car_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Car')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $id = Validate::parameter($request->get('id'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            if ($id) {
                if ($this->apiService->delete(Car::class, $id)) {
                    $this->common->deleteMultipleFiles($id, $user->getId(), "car");
                    return $this->respond(["message" => "Item deleted successfully"]);
                }
            }

            return $this->respondError(["message" => "Failed to delete item -" . $id]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
