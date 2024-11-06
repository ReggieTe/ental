<?php

namespace App\Controller;

use App\Entity\Images\Image;
use App\Entity\UserAdmin;
use App\Service\Common;
use App\Util\FileSystem;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ImageUploadController extends AbstractController
{
    private $common;
    private $fileSystem;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine,
        Common $common,
        FileSystem $fileSystem) {
        $this->common = $common;
        $this->fileSystem = $fileSystem;
        $this->doctrine = $doctrine;

    }
    #[Route("/dashboard/upload", name: "upload_image", methods: ["GET", "POST"])]
    public function upload(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        try {
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
            }
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
        return $this->redirect($url);

    }
    #[Route("/dashboard/delete/images", name: "delete_image", methods: ["GET", "POST"])]
    public function delete(#[CurrentUser] UserAdmin $user, Request $request): Response
    {
        try {
            $type = $request->request->get('type');
            $id = $request->request->get('id');
            $url = $request->request->get('url');

            if ($type) {
                $sortedObjects = $this->common->sortObjects($type);
                $path = $sortedObjects['path'];
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
