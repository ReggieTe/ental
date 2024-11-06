<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Images\AccountDocument;
use App\Entity\Images\Eft;
use App\Entity\Rental;
use App\Entity\UserAppNotification;
use App\Entity\UserDrivingRestriction;
use App\Entity\UserOtp;
use App\Entity\UserSetting;
use App\Entity\UserTripChecklist;
use App\Service\Common;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Util\FileSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;

class CloseController extends AbstractController
{
       private $dashboardServicePermissions;
    private $doctrine;
   private $fileSystem;
   private $notification;

    public function __construct(
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions ,
        FileSystem $fileSystem,
        Notification $notification
        )
    {        
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
        $this->fileSystem =  $fileSystem;
        $this->notification = $notification;   
    }
#[Route("/dashboard/close", name:"User Close")]
    
    public function index(#[CurrentUser] UserAdmin $user): Response
    { 
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/close/index.html.twig', [
            'permissions' => $this->dashboardServicePermissions->getDashboardPermission($user),
            'site' => $this->dashboardServicePermissions->getSiteDetails()
        ]);
    } 

#[Route("/dashboard/account/delete", name:"Delete User Account", methods:["DELETE","GET"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,Common $common):Response {       
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $email =  $user->getEmail();
        $entityManager = $this->doctrine->getManager();       
        $entities = [
            ["class"=>Rental::class ,"file"=>Eft::class ,'key'=>'user'],
            //["class"=>Car::class ,"file"=>Image::class,'key'=>"owner"],
            ["class"=>AccountDocument::class ,"file"=>AccountDocument::class,'key'=>"owner"]
        ];
        //Clean all files
            foreach($entities as $entity) {
                $items = $this->doctrine->getRepository($entity['class'])->findBy([$entity['key']=>$user->getId()]);
                foreach($items as $item) {
                    $files = $this->doctrine->getRepository($entity['file'])->findBy([$entity['key']=>$user->getId()]);
                    foreach($files as $file) {
                        $this->fileSystem->deleterUseFile($file->getImage(), $user->getId(), $file->getType());
                    }
                    $entityManager->remove($item);
                    $entityManager->flush();
                }
            }

        //clean all entities
        $entities = [
            ['class'=>UserAppNotification::class ,'key'=>"user"],
            //['class'=>AppUserNotification::class ,'key'=>"client"],
            ['class'=>UserDrivingRestriction::class,'key'=>"owner"],
            ['class'=>UserOtp::class,'key'=>"addedby"],
            ['class'=>UserSetting::class,'key'=>"addedby"],
            ['class'=>UserTripChecklist::class,'key'=>"owner"],
        ];

        foreach($entities as $entity) {
            $items = $this->doctrine->getRepository($entity['class'])->findBy([$entity['key']=>$user]);
                foreach($items as $item) {
                    $entityManager->remove($item);
                    $entityManager->flush();
                }
            }        

        //final delete user
        $this->fileSystem->deleterAllUserDirectory($user->getId());
        $entityManager->remove($user);
        $entityManager->flush();        
        $request->getSession()->invalidate();

        //Send email 
        $appData = $this->dashboardServicePermissions->getSiteDetails(); 
        $this->notification->sendEmail([            
            "from"=>$appData["email"],
            "to"=>$email,
            "replyTo"=>$appData["email"],
            "subject"=>'We’re sorry to see you go',
            "text"=>"We’re sorry to see you go",
            "template"=>"email/templates/delete.account.html.twig",
            "context"=>[
                'user' => $user,
            ]            
        ]);



        return $this->redirect("/home");
 }

 
}
