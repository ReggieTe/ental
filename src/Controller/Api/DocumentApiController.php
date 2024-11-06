<?php

namespace App\Controller\Api;

use App\Entity\Images\AccountDocument;
use App\Service\ApiService;
use App\Service\Common;
use App\Service\Validate;
use App\Util\FileSystem;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api', name: 'api_')]
class DocumentApiController extends AbstractApiController
{
    private $apiService;
    private $doctrine;
    private $common;
    private $fileSystem;

    public function __construct(
        ManagerRegistry $doctrine, ApiService $apiService,
        FileSystem $fileSystem,
        Common $common) {
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
        $this->common = $common;
        $this->doctrine = $doctrine;
        $this->fileSystem = $fileSystem;
    }

    #[Route("/v1/documents", name: "documents", methods: ["GET"])]
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
    #[OA\Tag(name: 'Document')]
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
            $documents = $this->common->getFiles($user->getId(), $user->getId(), 'document');
            $user = $user;
            return $this->respond(['list' => $documents]);
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/document/upload", name: "document_add", methods: ["POST"])]
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
    #[OA\Tag(name: 'Document')]
    #[Security(name: 'Token')]
    public function uploadDocument(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $file = $request->files->get('file');
            $type = strtolower($request->request->get('type'));
            $user = $user;
            $id = $user->getId();
            if ($file && $type) {
                if ($file->getMimeType() == "application/pdf") {
                    $imageObject = new AccountDocument();
                    $entityManager = $this->doctrine->getManager();
                    //delete if user has already have the item
                    $item = $this->doctrine->getRepository(AccountDocument::class)->findOneBy(['owner' => $id, 'type' => $type]);
                    if ($item) {
                        $entityManager->remove($item);
                        $entityManager->flush();
                        $this->fileSystem->deleterUseFile($item->getImage(), $id, "document");
                        //$this->addFlash('warning', "Note : Old file replace by new file");
                    }

                    $newFilename = str_replace(" ", "-", $type) . "-$id-" . uniqid() . '.pdf';
                    $destination = $this->fileSystem->getPath($id, "document");
                    $file->move($destination, $newFilename);
                    $localImage = $destination . $newFilename;
                    if (is_file($localImage)) {

                        $imageObject->setImage($newFilename);
                        $imageObject->setType($type);
                        $imageObject->setApproved(0);
                        $imageObject->setOwner($id);
                        $entityManager->persist($imageObject);
                        $entityManager->flush();
                        return $this->respond(["message" => "Uploaded successfully"]);
                    } else {
                        return $this->respondError(["message" => "Error Uploading file .Please try again"]);
                    }
                } else {
                    return $this->respondError(["message" => "Invalid type ! Only pdf is allowed.Please try again"]);
                }
            } else {
                return $this->respondError(["message" => "Error Uploading file .Please try again"]);
            }
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

    #[Route("/v1/document/delete", name: "document_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Document')]
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
            $id = $request->request->get('id');
            if ($id) {
                $item = $this->doctrine->getRepository(AccountDocument::class)->find($id);
                if ($item) {
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($item);
                    $entityManager->flush();
                    $this->fileSystem->deleterUseFile($item->getImage(), $user->getId(), "document");
                    return $this->respondError(["message" => "Item deleted successfully"]);
                } else {
                    return $this->respondError(["message" => "Failed to delete item"]);
                }
            } else {
                return $this->respondError(["message" => "Invalid item.Please try again later"]);
            }
        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

}
