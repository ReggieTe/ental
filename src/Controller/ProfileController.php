<?php

namespace App\Controller;

use App\Entity\Images\AccountDocument;
use App\Entity\Images\ImageProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProfileType;
use App\Service\Common;
use App\Service\DashboardService;
use App\Util\FileSystem;
use Symfony\Component\HttpFoundation\RequestStack;

class ProfileController extends AbstractController
{
    
        private $dashboardServicePermissions;
    private $doctrine;
    private $requestStack;
    private $common;
    private $fileSystem;

    public function __construct(
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,
        RequestStack $requestStack,
        FileSystem $fileSystem,
        Common $common)
    {
        $this->requestStack = $requestStack;
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ; 
        $this->common = $common; 
        $this->doctrine = $doctrine;
        $this->fileSystem = $fileSystem;
    }
#[Route("dashboard/profile", name:"User Profile")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $admin = $user; 
        $formTitle = 'Profile';
        $session = $this->requestStack->getSession();
        if(empty($session->get('email'))){
            $session->set('email', $admin->getEmail()); 
        }
        if (!empty($admin->getPhone())) {
            if (empty($session->get('phone'))) {
                $session->set('phone', $admin->getPhone());
            }
        }
        $images = $this->common->getFiles($admin->getId(),$admin->getId(),"profile");
        $form = $this->createForm(ProfileType::class, $admin);    
        $form->handleRequest($request);   
        if ($form->isSubmitted() && $form->isValid()) {
                $formEmail = $form->get('email')->getData();       
                $sessionEmail = $session->get('email');
                if($formEmail!=$sessionEmail){                    
                    $admin->setEmailVerified(0); //Email Changed ,unverify               
                }
                if (!empty($admin->getPhone())) {
                    $formPhone = $form->get('phone')->getData();
                    if (!empty($formPhone)) {
                        $sessionPhone = $session->get('phone');
                        if ($formPhone!=$sessionPhone) {
                            $admin->setPhoneVerified(0); //Phone Changed ,unverify
                        }
                    }
                }


            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();    
            $this->addFlash('success', $formTitle.' was updated successfully');
        }

        return $this->render('dashboard/profile/index.html.twig', [
            'form' => $form,
            'title'=>$formTitle,
            'images'=>$images,
            'isPhoneVerified'=>$admin->getPhoneVerified()?true:false,
            'isEmailVerified'=>$admin->getEmailVerified()?true:false,
            'verifyPhone'=>$admin->getPhone()?($admin->getPhoneVerified()?false:true):false,
            'verifyEmail'=>$admin->getEmail()?($admin->getEmailVerified()?false:true):false,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($admin),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }
    
    #[Route("/dashboard/profile/verification", name:"uploadVerification", methods:["GET","POST"])]
    public function uploadVerification(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        try{
            $file = $request->files->get('file');
            $type = strtolower($request->request->get('type'));
            
            $id = $user->getId();       
                if ($file&&$type) {
                            $imageObject = new AccountDocument();
                            $newFilename =str_replace(" ","*",$type)."-$id-".uniqid().'.png';    
                            $destination = $this->fileSystem->getPath($id,"document");   
                            dd($destination);             
                            $file->move($destination, $newFilename); 
                            $localImage = $destination.$newFilename;
                            if (is_file($localImage)) {
                                $entityManager = $this->doctrine->getManager();
                                $imageObject->setImage($newFilename);
                                $imageObject->setType($type);
                                $imageObject->setApproved(0);
                                $imageObject->setOwner($id);
                                $entityManager->persist($imageObject);
                                $entityManager->flush();
                                $this->addFlash('success',"Uploaded successfully");                         
                            }else{
                                $this->addFlash('danger',"Error Uploading the profile image .Please try again");
                            }
                }else{
                    $this->addFlash('danger',"Error Uploading the profile image .Please try again");
                }       
    } catch (\Exception $e) {
        $this->addFlash('danger',$e->getMessage()); 
    }
    return $this->redirect("/dashboard/account/verify");

    }
#[Route("/dashboard/upload/profile", name:"profile_upload_image", methods:["GET","POST"])]
    public function upload(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        try{
            $file = $request->files->get('file');
            
            $type = "profile";
            $id = $user->getId();      
            if ($file) {
                if($file->getMimeType() =="image/png"||$file->getMimeType() =="image/jpeg") {
                    $imageObject = new ImageProfile();
                    $newFilename ="$type-$id-".uniqid().'.png';
                    $destination = $this->fileSystem->getPath($id, "profile");
                    $file->move($destination, $newFilename);
                    $localImage = $destination.$newFilename;
                    if (is_file($localImage)) {
                        $entityManager = $this->doctrine->getManager();
                        $imageObject->setImage($newFilename);
                        $imageObject->setOwner($id);
                        $imageObject->setName($type);
                        $entityManager->persist($imageObject);
                        $entityManager->flush();
                        $this->addFlash('success', "Profile image uploaded successfully");

                    }
                } else {
                    $this->addFlash('danger', "Invalid type ! Only png/jpg is allowed.Please try again");
                }
            }else{
                                $this->addFlash('danger',"Error Uploading the profile image .Please try again");
                            } 
    } catch (\Exception $e) {
        $this->addFlash('danger',$e->getMessage()); 
    }
    return $this->redirect("/dashboard/profile");

    }
#[Route("/dashboard/profile/delete/images", name:"profile_delete_image", methods:["GET","POST"])] 
    public function deleteImage(#[CurrentUser] UserAdmin $user,Request $request) :Response {
        try{
            $type = "profile";
            
            $id = $request->request->get('id');
                 $objectToDelete = $this->doctrine->getRepository(ImageProfile::class)->find($id);
                if ($objectToDelete) {
                    $imageName = $objectToDelete->getImage();
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->remove($objectToDelete);
                    $entityManager->flush();                    
                     $this->fileSystem->deleterUseFile($imageName, $user->getId(), "profile");

                    $this->addFlash('success', "Profile image deleted successfully");
                }else{
                    $this->addFlash('warning',"Profile image not found");  
                }
        } catch (\Exception $e) {
            $this->addFlash('danger',$e->getMessage()); 
        }
        return $this->redirect("/dashboard/profile");
    }     
     
}
