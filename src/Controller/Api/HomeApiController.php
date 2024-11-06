<?php

namespace App\Controller\Api;

use App\Entity\Enum\AgreedEnum;
use App\Entity\Enum\BankAccountTypeEnum;
use App\Entity\Enum\BankEnum;
use App\Entity\Enum\BrandEnum;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\Enum\DocumentEnum;
use App\Entity\Enum\FuelEnum;
use App\Entity\Enum\PaymentEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Enum\SectionEnum;
use App\Entity\Enum\TransmissionEnum;
use App\Service\ApiService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class HomeApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, ApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
    }

    #[Route("/v1/app/data", name: "app_data", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            return $this->respond([
                'app' => $this->apiService->getAppSettings(),
                'agreed' => $this->apiService->processEnum(AgreedEnum::choices()),
                'bankAccountType' => $this->apiService->processEnum(BankAccountTypeEnum::choices()),
                'bank' => $this->apiService->processEnum(BankEnum::choices()),
                'brand' => $this->apiService->processEnum(BrandEnum::choices()),
                'checklist' => $this->apiService->processEnum(ChecklistEnum::choices()),
                'document' => $this->apiService->processEnum(DocumentEnum::choices()),
                'fuel' => $this->apiService->processEnum(FuelEnum::choices()),
                'payment' => $this->apiService->processEnum(PaymentEnum::choices()),
                'paymentStatus' => $this->apiService->processEnum(PaymentStatusEnum::choices()),
                'rental' => $this->apiService->processEnum(RentalEnum::choices()),
                'transmission' => $this->apiService->processEnum(TransmissionEnum::choices()),
                'filters' => [
                    'fuel' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'fuel'), false),
                    'transmission' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'transmission'), false),
                    'brand' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'brand'), false),
                    'price' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'price'), false),
                    'deposit' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'deposit'), false),
                    'payment' => $this->apiService->processEnum($this->apiService->prepareFilters([], 'payment'), false),
                ]
            ]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("v1/validate/token", name: "validated_token", methods: ["POST"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $userToken = $request->get('token') != null ? $request->get('token') : null;
            $user = $this->apiService->validateToken($userToken);
            if ($user != null) {
                $user = $user['object'];
                if ($user->getToken() == $userToken) {
                    return $this->respond(['valid' => true, "message" => "Valid token"]);
                }
            }
            return $this->respondError(["message" => "Invalid token"]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/help", name: "help", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function help()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::TERMS)]);
    }
    #[Route("/v1/app/terms", name: "app_terms", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function terms()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::TERMS)]);
    }
    #[Route("/v1/app/faqs", name: "app_faqs", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function faqs()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::FAQS)]);
    }
    #[Route("/v1/app/disclaimer", name: "app_disclaimer", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function disclaimer()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::DISCLAIMER)]);
    }
    #[Route("/v1/app/privacy", name: "app_privacy", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function privacy()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::PRIVACY)]);
    }
    #[Route("/v1/app/about", name: "app_about", methods: ["GET"])]
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
    #[OA\Tag(name: 'Home')]
    #[Security(name: 'Token')]
    public function about()
    {
        return $this->respond(["info" => $this->apiService->getInformation(SectionEnum::FAQS)]);
    }

}
