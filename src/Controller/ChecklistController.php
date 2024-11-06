<?php

namespace App\Controller;

use App\Entity\Enum\AgreedEnum;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Rental;
use App\Entity\UserCarAvailableItem;
use App\Entity\UserCarIssue;
use App\Entity\UserCarMissingItem;
use App\Entity\UserTripChecklist;
use App\Form\ChecklistType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use App\Service\DashboardService;
use App\Service\Notification;

class ChecklistController extends AbstractController
{
    
    private $apiService;
        private $dashboardServicePermissions;
    private $doctrine;
    private $notification;

    public function __construct(ApiService $apiService,
    ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,
    Notification $notification
    )
    {
        $this->apiService = $apiService;
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;
       $this->notification = $notification;        
    }

#[Route("/dashboard/checklist/add/{rentalId?}/{id?}", name:"Add or Edit Checklist", methods:["GET","POST"])]
    
    public function add(#[CurrentUser] UserAdmin $user,Request $request,?string $id,?string $rentalId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = new UserTripChecklist; 
        
        $formTitle = 'Add checklist';
        $issues = [];
        $availableItems = [];
        $missingItems = [];
        //Check if rental doesn't have the checklist the user is trying to create 
        if (empty($id)) {
            $type = $request->request->get('checklist');
                if($type) {                    
                    $instances = $this->doctrine->getRepository(UserTripChecklist::class)->findBy(['type'=>$type['type'],'rental'=>$rentalId]);
                    if(count($instances)) {
                        $existingChecklist =  current($instances);
                        //Load the checklist
                        $instance = $existingChecklist;
                    }
                }
            }

        
        if (!empty($id)) {
            $formTitle = "Update checklist";
            $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            $issueList = $this->doctrine->getRepository(UserCarIssue::class)->findBy(['checklist'=>$instance]);
            $issues = $this->apiService->process($issueList);
            
            $availableItemsList = $this->doctrine->getRepository(UserCarAvailableItem::class)->findBy(['checklist'=>$instance]);
            $availableItems = $this->apiService->process($availableItemsList);
            
            $missingItemslist = $this->doctrine->getRepository(UserCarMissingItem::class)->findBy(['checklist'=>$instance]);
            $missingItems = $this->apiService->process($missingItemslist);
        }

        $form = $this->createForm(ChecklistType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $rental = $this->doctrine->getRepository(Rental::class)->find($rentalId);
            if($rental) {
            $instance->setOwner($user);
            $instance->setRental($rental);
            $instance->setStatus(PaymentStatusEnum::PROCESSING);
            $entityManager->persist($instance);
            $entityManager->flush();
            $requestRenterToSign = $request->request->get('checklist');
           if($requestRenterToSign){
                //dd($requestRenterToSign);
                // send email to renter to review and sign checklist
                if(isset($requestRenterToSign['requestRenterToSign'])) {                         
                    $appData = $this->dashboardServicePermissions->getSiteDetails();
                    $this->notification->sendEmail([            
                        "from"=>$appData["email"],
                        "to"=>$rental->getUser()->getEmail(),
                        "replyTo"=>$appData["email"],
                        "subject"=>"Rental #".$rental->getId()." Checklist review",
                        "text"=>"Rental #".$rental->getId()." Checklist review",
                        "template"=>"email/templates/checklist.html.twig",
                        "context"=>[
                            'user' => $this->apiService->sortObjectToArray($rental->getUser()),
                            'invoice'=>['id'=>$rental->getId()],
                            'checklist'=>$this->apiService->sortObjectToArray($instance),
                        ]            
                    ]);

                    $this->notification->sendNotification($rental->getUser(),[
                        'header'=>"Rental #".$rental->getId()." Checklist review",
                        'body'=>"Rental #".$rental->getId()." Checklist review.Check email for full details!"
                    ]);
                }
           }
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(ChecklistType::class, $instance);
            $id = $instance->getId();
        }
        }

        return $this->render('dashboard/checklist/index.html.twig', [
            'form' => $form,            
            'id' =>$id,
            'rentalId'=>$rentalId,
            'issues' =>$issues,
            'availableItems' =>$availableItems,
            'missingItems' =>$missingItems,
            'title'=>$formTitle, 
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),           
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }

#[Route("/dashboard/checklist/view/{rentalId?}/{id?}", name:"View Checklist", methods:["GET","POST"])]
    
    public function view(#[CurrentUser] UserAdmin $user,Request $request,?string $id,?string $rentalId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = new UserTripChecklist; 
        
        $checklist = 'Checklist';
        $issues = [];
        $availableItems = [];
        $missingItems = [];
        $additionItems = [];
        $restrictions = [];
        $types = array_flip(AgreedEnum::choices());
        
        //if (!empty($id)) {
            $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            $issueList = $this->doctrine->getRepository(UserCarIssue::class)->findBy(['checklist'=>$instance]);
            $issues = $this->apiService->process($issueList);
            
            $availableItemsList = $this->doctrine->getRepository(UserCarAvailableItem::class)->findBy(['checklist'=>$instance]);
            $availableItems = $this->apiService->process($availableItemsList);
              
            
            $missingItemslist = $this->doctrine->getRepository(UserCarMissingItem::class)->findBy(['checklist'=>$instance]);
            $missingItems = $this->apiService->process($missingItemslist);
            $vehicle = $instance->getRental()->getCar();
            $vehiclePaidDeposit = $vehicle->getRefundableDeposit();
            foreach($missingItemslist as $item) {
                $vehiclePaidDeposit =  (float)$vehiclePaidDeposit - ((int)$item->getMeasurement()*(float)$item->getAmount());
            }
        //} 

        $vehicle = $instance->getRental()->getCar();       
        if($vehicle) {
            $additionals = $vehicle->getOwner()->getUserCarAdditionals();
            $additionalTotalToAdd = 0;
            foreach($additionals as $additional) {
                $additionalTotalToAdd = $additionalTotalToAdd + $additional->getAmount();
                array_push($additionItems, ['description'=>$additional->getDescription(),'amount'=>$additional->getAmount()]);
            }
            $restrictionList = $vehicle->getOwner()->getUserDrivingRestrictions();
            $restrictions = $this->apiService->process($restrictionList);
        }

        return $this->render('dashboard/checklist/view/index.html.twig', [
            'checklist' => $this->apiService->sortObjectToArray($instance),  
            'issues' =>$issues,
            'availableItems' =>$availableItems,
            'missingItems' =>$missingItems,
            'restrictions'=>$restrictions,
            'additionals'=>$additionals,
            'refundableDeposit'=>$vehiclePaidDeposit,
            "types"=>$types,
            'title'=>'Checklist', 
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),           
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }

#[Route("/dashboard/sign/document", name:"SignDocument", methods:["GET","POST"])]
    
    public function signDocument(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $agree = $request->request->get('agree');
        $id = $request->request->get('id'); 
        $rentalId = $request->request->get('rental');
        if(!empty($agree)){
            $entityManager = $this->doctrine->getManager();
            $instance = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            if($instance){                
                if($agree==AgreedEnum::YES){
                    $instance->setDateSignedByRenter(new \DateTime('now'));
                    $instance->setRenterAgreed($agree);
                    $instance->setStatus(PaymentStatusEnum::DONE);
                    $entityManager->persist($instance);
                    $entityManager->flush();
                    //Send Email     
                    $appData = $this->dashboardServicePermissions->getSiteDetails();             
                    $this->notification->sendEmail([            
                        "from"=>$appData["email"],
                        "to"=>$instance->getRental()->getCar()->getOwner()->getEmail(),
                        "replyTo"=>$appData["email"],
                        "subject"=>'Rental #'.$instance->getRental()->getId().' Checklist approved',
                        "text"=>'Rental #'.$instance->getRental()->getId().' Checklist approved',
                        "template"=>"email/templates/checklist.approved.html.twig",
                        "context"=>[
                            'user' => $instance->getRental()->getCar()->getOwner(),
                        ]
                    ]);
                    
                    $this->notification->sendNotification($instance->getRental()->getCar()->getOwner(),[
                        'header'=>'Rental #'.$instance->getRental()->getId().' Checklist approved',
                        'body'=>'Rental #'.$instance->getRental()->getId().' Checklist approved.Check email for full details!'
                    ]);
                    //IF END OF RESERVATION 
                    //Calcalate balance 
                    if($instance->getType()== ChecklistEnum::END){
                        //$instance->get
                        $rental =  $instance->getRental();
                        $qouteAmount = $rental->getQuoteAmount();
                        $issues  = $instance->getUserCarAvailableItems();
                        foreach($issues as $issue){
                            $qouteAmount = $qouteAmount + ((float)$issue->getAmount()*(int)$issue->getMeasurement());  
                        }
                        $rental->setPaidAmount($qouteAmount);
                        $rental->setStatus(RentalEnum::DONE);                        
                        $vehicle=$rental->getCar();
                        $vehicle->setBooked(false);
                        $entityManager->persist($rental);
                        $entityManager->persist($vehicle);
                        $entityManager->flush();                        
                    }

                    $this->addFlash('success', 'Updated successfully');
                }else{                    
                    $this->addFlash('warning', 'Check with the car owner so that the issue is resolved successfully');
                }
                
            }else{
                $this->addFlash('danger', 'Failed to update checklist'); 
            }
        }else{
            $this->addFlash('danger', 'Select correct item to proceed');
        }

        return $this->redirect("/dashboard/checklist/view/$rentalId/$id");
    }

#[Route("/dashboard/issue/add", name:"addIssue", methods:["GET","POST"])]
    
    public function addIssue(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $issue = $request->request->get('issue');
        $id = $request->request->get('id'); 
        $rentalId = $request->request->get('rental');

        if($issue){
            $entityManager = $this->doctrine->getManager();
            $instance = new UserCarIssue();
            $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            if($trip){
                $instance->setChecklist($trip);
                $instance->setDescription($issue);
                $entityManager->persist($instance);
                $entityManager->flush();
                $this->addFlash('success', 'Issue added successfully');
            }else{
                $this->addFlash('danger', 'Failed to add issue'); 
            }
        }else{

            $this->addFlash('danger', 'Issue must not be blank');
        }

        return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
    }
        
    
    #[Route("/dashboard/issue/delete/{rentalId}/{id}/{item}", name:"Delete Issue", methods:["GET","POST"])]
    
    public function deleteIssue(#[CurrentUser] UserAdmin $user,Request $request,$id,$rentalId,$item) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
        $deleteItem = $this->doctrine->getRepository(UserCarIssue::class)->find($item);
        if($deleteItem){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($deleteItem);
            $entityManager->flush();
            $this->addFlash('success', "Issue item deleted successfully");            
        }
        
        return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
     }   
     
      #[Route("/dashboard/available/item/delete/{rentalId}/{id}/{item}", name:"deleteAvailableItem", methods:["GET","POST"])]
     
     public function deleteAvailableItem(#[CurrentUser] UserAdmin $user,Request $request,$id,$rentalId,$item) {     
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
         $deleteItem = $this->doctrine->getRepository(UserCarAvailableItem::class)->find($item);
         if($deleteItem){
             $entityManager = $this->doctrine->getManager();
             $entityManager->remove($deleteItem);
             $entityManager->flush();
             $this->addFlash('success', "Issue item deleted successfully");            
         }
         
         return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
      }   
      
        #[Route("/dashboard/missing/item/delete/{rentalId}/{id}/{item}", name:"deleteMissingItem", methods:["GET","POST"])]
      
      public function deleteMissingItem(#[CurrentUser] UserAdmin $user,Request $request,$id,$rentalId,$item) {     
          $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
          $deleteItem = $this->doctrine->getRepository(UserCarMissingItem::class)->find($item);
          if($deleteItem){
              $entityManager = $this->doctrine->getManager();
              $entityManager->remove($deleteItem);
              $entityManager->flush();
              $this->addFlash('success', "Issue item deleted successfully");            
          }          
          return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
       }

#[Route("/dashboard/available/item/add", name:"addAvailableItem", methods:["GET","POST"])]
    
    public function addAvailableItem(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->request->get('amount');
        $measurement = $request->request->get('measurement');
        $description = $request->request->get('description');
        $id = $request->request->get('id'); 
        $rentalId = $request->request->get('rental');

        if($description&&$amount&&$measurement){
            $entityManager = $this->doctrine->getManager();
            $instance = new UserCarAvailableItem();
            $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            if($trip){
                $instance->setChecklist($trip);
                $instance->setAmount($amount);
                $instance->setMeasurement($measurement);
                $instance->setDescription($description);
                $entityManager->persist($instance);
                $entityManager->flush();
                $this->addFlash('success', 'item added successfully');
            }else{
                $this->addFlash('danger', 'Failed to add item'); 
            }
        }else{

            $this->addFlash('danger', "All fields must be filled $description $amount $measurement");
        }

        return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
    }

#[Route("/dashboard/missing/item/add", name:"addMissingItem", methods:["GET","POST"])]
    
    public function addMissingItem(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->request->get('amount');
        $measurement = $request->request->get('measurement');
        $description = $request->request->get('description');
        $id = $request->request->get('id'); 
        $rentalId = $request->request->get('rental');


        if($description&&$amount&&$measurement){
            $entityManager = $this->doctrine->getManager();
            $instance = new UserCarMissingItem();
            $trip = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
            if($trip){
                $instance->setChecklist($trip);
                $instance->setAmount($amount);
                $instance->setMeasurement($measurement);
                $instance->setDescription($description);
                $entityManager->persist($instance);
                $entityManager->flush();
                $this->addFlash('success', 'item added successfully');
            }else{
                $this->addFlash('danger', 'Failed to add item');  
            }
        }else{

            $this->addFlash('danger', 'Issue must not be blank');
        }

        return $this->redirect("/dashboard/checklist/add/$rentalId/$id");
    }
        
    
    #[Route("/dashboard/checklist/delete/{rentalId}/{id?}", name:"Delete Checklist", methods:["GET","POST"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id,$rentalId) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
        $checklist = $this->doctrine->getRepository(UserTripChecklist::class)->find($id);
        if($checklist){
            if($checklist->getStatus()!=PaymentStatusEnum::DONE){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($checklist);
            $entityManager->flush();
            $this->addFlash('success', "Checklist item deleted successfully");
            }else{
                $this->addFlash('danger', "Checklist can't be deleted ,it's closed");
            }
        }        
        return $this->redirect("/dashboard/rental/view/$rentalId");
     }
     
}
