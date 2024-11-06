<?php

namespace App\Controller\Api;

use App\Entity\Car;
use App\Entity\Images\AccountDocument;
use App\Entity\Images\Eft;
use App\Entity\Rental;
use App\Entity\UserAppNotification;
use App\Entity\UserDrivingRestriction;
use App\Entity\UserOtp;
use App\Entity\UserSetting;
use App\Entity\UserTripChecklist;
use App\Service\ApiService;
use App\Service\Validate;
use App\Util\FileSystem;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class CloseApiController extends AbstractApiController
{
    private $fileSystem;
    private $apiService;
    private $doctrine;

    public function __construct(
        FileSystem $fileSystem,
        ManagerRegistry $doctrine, ApiService $apiService
    ) {
        $this->fileSystem = $fileSystem;
        $this->apiService = $apiService;
    }

    #[Route("/v1/account/delete", name: "close_account", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Close')]
    #[Security(name: 'Token')]
    public function delete(Request $request): JsonResponse
    {
        try {

            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $entityManager = $this->doctrine->getManager();
            $entities = [
                ['class' => Car::class, 'key' => "owner"],
                ['class' => Rental::class, 'key' => "owner"],
                ['class' => AccountDocument::class, 'key' => "user"],
                ['class' => Eft::class, 'key' => "user"],
            ];
            //Clean all files
            foreach ($entities as $entity) {
                $items = $this->doctrine->getRepository($entity['class'])->findBy([$entity['key'] => $user->getId()]);
                foreach ($items as $item) {
                    $files = $this->doctrine->getRepository($entity['file'])->findBy([$entity['key'] => $user->getId()]);
                    foreach ($files as $file) {
                        $this->fileSystem->deleterUseFile($file->getImage(), $user->getId(), $file->getType());
                    }
                    $entityManager->remove($item);
                    $entityManager->flush();
                }
            }

            //clean all entities
            $entities = [
                ['class' => UserAppNotification::class, 'key' => "user"],
                ['class' => UserDrivingRestriction::class, 'key' => "owner"],
                ['class' => UserOtp::class, 'key' => "addedby"],
                ['class' => UserSetting::class, 'key' => "addedby"],
                ['class' => UserTripChecklist::class, 'key' => "owner"],
            ];

            foreach ($entities as $entity) {
                $items = $this->doctrine->getRepository($entity['class'])->findBy([$entity['key'] => $user]);
                foreach ($items as $item) {
                    $entityManager->remove($item);
                    $entityManager->flush();
                }
            }

            //final delete user
            $this->fileSystem->deleterAllUserDirectory($user->getId());
            $entityManager->remove($user);
            $entityManager->flush();
            $request->getSession()->invalidate();
            return $this->respondError(["message" => "Successfully deleted"]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
