<?php

namespace App\Controller\Api;

use App\Entity\UserPaypal;
use App\Form\Api\PayPalType;
use App\Service\ApiService;
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
class PayPalApiController extends AbstractApiController
{

    private $apiService;
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine, ApiService $apiService) {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }
    #[Route("/v1/profile/paypal", name: "paypal_index", methods: ["POST"])]
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
    #[OA\Tag(name: 'PayPal')]
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
            $instance = $user->getUserPayPal();
            $formTitle = 'Add Paypal Account';

            if (!$instance) {
                $instance = new UserPayPal();
                $formTitle = "Update  Paypal Account";
            }
            $form = $this->buildForm(PayPalType::class, $instance);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->doctrine->getManager();
                $instance->setClient($user);
                $entityManager->persist($instance);
                $entityManager->flush();
                return $this->respondError(["message" => $formTitle . ' was successfully']);
            }
            return $this->respondError([
                'message' => "Form contains errors",
                'errors' => Form::ErrorMessages($form),
            ]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

    #[Route("/v1/profile/paypal/delete", name: "paypal_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'PayPal')]
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
            if ($this->apiService->delete(UserPayPal::class, $id)) {
                return $this->respond(["message" => "Item deleted successfully"]);
            } else {
                return $this->respondError(["message" => "Failed to delete item"]);
            }
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
