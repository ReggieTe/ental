<?php

namespace App\Controller\Api;

use App\Entity\Images\ImageProfile;
use App\Form\Api\ProfileType;
use App\Service\ApiService;
use App\Service\Common;
use App\Service\Validate;
use App\Util\FileSystem;
use App\Util\Form;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProfileApiController extends AbstractApiController
{

    private $apiService;
    private $doctrine;
    private $requestStack;
    private $common;
    private $fileSystem;

    public function __construct(
        ManagerRegistry $doctrine, ApiService $apiService,
        RequestStack $requestStack,
        FileSystem $fileSystem,
        Common $common) {
        $this->requestStack = $requestStack;
        $this->apiService = $apiService;
        $this->doctrine = $doctrine;
        $this->common = $common;
        $this->doctrine = $doctrine;
        $this->fileSystem = $fileSystem;
    }
    #[Route("/v1/profile", name: "profile_index", methods: ["POST"])]
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
    #[OA\Tag(name: 'Profile')]
    #[Security(name: 'Token')]
    public function index(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $admin = $user['object'];
            $formTitle = 'Profile';
            $session = $this->requestStack->getSession();
            if (empty($session->get('email'))) {
                $session->set('email', $admin->getEmail());
            }
            if (!empty($admin->getPhone())) {
                if (empty($session->get('phone'))) {
                    $session->set('phone', $admin->getPhone());
                }
            }
            $images = $this->common->getFiles($admin->getId(), $admin->getId(), "profile");
            $form = $this->buildForm(ProfileType::class, $admin);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $formEmail = $form->get('email')->getData();
                $sessionEmail = $session->get('email');
                if ($formEmail != $sessionEmail) {
                    $admin->setEmailVerified(0); //Email Changed ,unverify
                }
                if (!empty($admin->getPhone())) {
                    $formPhone = $form->get('phone')->getData();
                    if (!empty($formPhone)) {
                        $sessionPhone = $session->get('phone');
                        if ($formPhone != $sessionPhone) {
                            $admin->setPhoneVerified(0); //Phone Changed ,unverify
                        }
                    }
                }

                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($admin);
                $entityManager->flush();
                return $this->respond(['message' => $formTitle . ' was updated successfully']);
            }
            return $this->respondError([
                "message" => "Error updating profile",
                "errors" => Form::ErrorMessages($form),

            ]);

        } catch (Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }
    #[Route("/v1/profile/upload/image", name: "profile_photo", methods: ["POST"])]
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
    #[OA\Tag(name: 'Profile')]
    #[Security(name: 'Token')]
    public function upload(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $file = $request->files->get('file');
            $user = $user;
            $type = "profile";
            $id = $user->getId();
            if ($file) {
                if ($file->getMimeType() == "image/png" || $file->getMimeType() == "image/jpeg") {
                    $imageObject = new ImageProfile();
                    $newFilename = "$type-$id-" . uniqid() . '.png';
                    $destination = $this->fileSystem->getPath($id, "profile");
                    $file->move($destination, $newFilename);
                    $localImage = $destination . $newFilename;
                    if (is_file($localImage)) {
                        $entityManager = $this->doctrine->getManager();
                        $imageObject->setImage($newFilename);
                        $imageObject->setOwner($id);
                        $imageObject->setName($type);
                        $entityManager->persist($imageObject);
                        $entityManager->flush();
                        return $this->respondError(["message" => "Profile image uploaded successfully"]);

                    }
                } else {
                    return $this->respondError(["message" => "Invalid type ! Only png/jpg is allowed.Please try again"]);
                }
            } else {
                return $this->respondError(["message" => "Error Uploading the profile image .Please try again"]);
            }
        } catch (\Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }
    }

    #[Route("/v1/profile/delete/image", name: "profile_photo_delete", methods: ["DELETE"])]
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
    #[OA\Tag(name: 'Profile')]
    #[Security(name: 'Token')]
    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $token = Validate::parameter($request->get('token'));
            $user = $this->apiService->validateToken($token);
            if ($user == null) {
                return $this->respondError(["message" => "Invalid token.Please login again"]);
            };
            $user = $user['object'];
            $type = "profile";
            $user = $user;
            $id = $request->request->get('id');
            $objectToDelete = $this->doctrine->getRepository(ImageProfile::class)->find($id);
            if ($objectToDelete) {
                $imageName = $objectToDelete->getImage();
                $entityManager = $this->doctrine->getManager();
                $entityManager->remove($objectToDelete);
                $entityManager->flush();
                $this->fileSystem->deleterUseFile($imageName, $user->getId(), "profile");

                return $this->respondError(["message" => "Profile image deleted successfully"]);
            } else {
                return $this->respondError(["message" => "Profile image not found"]);
            }
        } catch (\Exception $e) {
            return $this->respondError(["message" => $e->getMessage()]);
        }

    }

}
