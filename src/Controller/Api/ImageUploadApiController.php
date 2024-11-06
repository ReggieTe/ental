<?php

namespace App\Controller\Api;

use App\Entity\Images\Image;
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
class ImageUploadApiController extends AbstractApiController
{
    private $fileSystem;
    private $apiService;
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine, ApiService $apiService,
        FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;

    }
    #[Route("/v1/image/upload", name: "upload_photo", methods: ["POST"])]
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
    #[OA\Tag(name: 'Image Upload')]
    #[Security(name: 'Token')]
    public function upload(Request $request): JsonResponse
    {
        try { $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $file = $request->files->get('file');
            $type = $request->request->get('type');
            $id = $request->request->get('id');
            $url = $request->request->get('url');
            $owner = $request->request->get('owner');
            if ($type) {
                if ($file) {
                    if ($file->getMimeType() == "image/png" || $file->getMimeType() == "image/jpeg") {
                        $imageObject = new Image();
                        $newFilename = "$type-$id-" . uniqid() . '.png';
                        $destination = $this->fileSystem->getPath($owner, $type);
                        $file->move($destination, $newFilename);
                        $localImage = $destination . $newFilename;
                        if (is_file($localImage)) {
                            $entityManager = $this->doctrine->getManager();
                            $imageObject->setImage($newFilename);
                            $imageObject->setName($type);
                            $imageObject->setOwner($id);
                            $entityManager->persist($imageObject);
                            $entityManager->flush();
                            $this->addFlash('success', "Image Uploaded successfully");

                        } else {
                            $this->addFlash('danger', "Error Uploading the image .Please try again");
                        }
                    } else {
                        $this->addFlash('danger', "Invalid type ! Only png/jpg is allowed.Please try again");
                    }
                } else {
                    $this->addFlash('danger', "Invalid image .Please try again");
                }
            }} catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
        return $this->redirect($url);

    }

    #[Route("/v1/image/delete", name: "delete_photo", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Image Upload')]
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
            $type = $request->request->get('type');
            $id = $request->request->get('id');
            $url = $request->request->get('url');

            if ($type) {
                $objectToDelete = $this->doctrine->getRepository(Image::class)->find($id);
                if ($objectToDelete) {
                    $imageName = $objectToDelete->getImage();
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($objectToDelete);
                    $entityManager->flush();
                    $this->fileSystem->deleterUseFile($imageName, $user->getId(), $type);
                    $this->addFlash('success', "Image deleted successfully");
                } else {
                    $this->addFlash('warning', "Image not found");
                }
            }
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
        return $this->redirect($url);
    }

}
