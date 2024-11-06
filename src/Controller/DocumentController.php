<?php

namespace App\Controller;

use App\Entity\Enum\DocumentEnum;
use App\Entity\Images\AccountDocument;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Common;
use App\Service\DashboardService;
use App\Util\FileSystem;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DocumentController extends AbstractController
{
            private $dashboardServicePermissions;
    private $doctrine;   
        private $common;  
        private $fileSystem;
    
        public function __construct(
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,
        ParameterBagInterface $params,
        FileSystem $fileSystem,
        Common $common)
        {               
            $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ; 
            $this->common = $common; 
            $this->fileSystem = $fileSystem;
        }
#[Route("/dashboard/documents", name:"UploadDocuments")]
    
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $documents = $this->common->getFiles($user->getId(),$user->getId(),'document');
        
        return $this->render('dashboard/documents/index.html.twig', [
            'title'=>"Documents",
            'documents'=>$documents,
            
            'types'=>array_flip(DocumentEnum::choices()),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }
    #[Route("/dashboard/document/upload", name:"uploadDocument", methods:["GET","POST"])]
    
    public function uploadDocument(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        try{
            $file = $request->files->get('file');
            $type = strtolower($request->request->get('type'));
            
            $id = $user->getId();       
                if ($file&&$type) {
                    if($file->getMimeType() =="application/pdf") {
                        $imageObject = new AccountDocument();
                        $entityManager = $this->doctrine->getManager();
                        //delete if user has already have the item 
                        $item = $this->doctrine->getRepository(AccountDocument::class)->findOneBy(['owner'=>$id,'type'=>$type]);
                        if($item){
                            $entityManager->remove($item);
                            $entityManager->flush();
                            $this->fileSystem->deleterUseFile($item->getImage(), $id, "document");
                            $this->addFlash('warning', "Note : Old file replace by new file");
                        }

                        $newFilename =str_replace(" ", "-", $type)."-$id-".uniqid().'.pdf';
                        $destination = $this->fileSystem->getPath($id,"document");
                        $file->move($destination, $newFilename);
                        $localImage = $destination.$newFilename;
                        if (is_file($localImage)) {
                            
                            $imageObject->setImage($newFilename);
                            $imageObject->setType($type);
                            $imageObject->setApproved(0);
                            $imageObject->setOwner($id);
                            $entityManager->persist($imageObject);
                            $entityManager->flush();
                            $this->addFlash('success', "Uploaded successfully");
                        } else {
                            $this->addFlash('danger', "Error Uploading file .Please try again");
                        }
                    } else {
                        $this->addFlash('danger', "Invalid type ! Only pdf is allowed.Please try again");
                    }
                }else{
                    $this->addFlash('danger',"Error Uploading file .Please try again");
                }       
    } catch (Exception $e) {
        $this->addFlash('danger',$e->getMessage()); 
    }
    return $this->redirect("/dashboard/documents");

    }

        
    
    #[Route("/dashboard/document/delete", name:"deleteDocument", methods:["GET","POST"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
        $id = $request->request->get('id');
        if($id) {
            $item = $this->doctrine->getRepository(AccountDocument::class)->find($id);
            if($item) {
                $entityManager = $this->doctrine->getManager();
                $entityManager->remove($item);
                $entityManager->flush();
                $this->fileSystem->deleterUseFile($item->getImage(), $user->getId(), "document");
                $this->addFlash('success', "Item deleted successfully");
            } else {
                $this->addFlash('danger', "Failed to delete item");
            }
        } else{
            $this->addFlash('danger', "Invalid item.Please try again later");
        }      
        return $this->redirect("/dashboard/documents");
}
     
}
