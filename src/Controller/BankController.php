<?php

namespace App\Controller;

use App\Entity\UserBank;
use App\Form\BankType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use App\Service\DashboardService;

class BankController extends AbstractController
{
    
        private $dashboardServicePermissions;
    private $doctrine;
    private $apiService;

    public function __construct(
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,
        ApiService $apiService)
    {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ; 
        $this->apiService = $apiService; 
    }
#[Route("/dashboard/profile/accounts", name:"User Accounts")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/bank/index.html.twig', [           
            'title'=>"Accounts",
            'list'=> $this->apiService->process($user->getUserBanks()),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    
    }
    
    #[Route("/dashboard/profile/account/add/{id?}", name:"Save Bank Accounts", methods:["GET","POST"])]
    
    public function saveBank(#[CurrentUser] UserAdmin $user,Request $request,$id): Response
    {
        $instance = new UserBank();
        
        $formTitle = 'Add Account';
        
        if ($id) {
            $item = $this->doctrine->getRepository(UserBank::class)->find($id);
            if($item){
                
            $formTitle = "Update account";
                $instance = $item;
            }
        }           
        $form = $this->createForm(BankType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setClient($user);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(BankType::class, $instance);  
            $id = $instance->getId();
        }

        return $this->render('dashboard/bank/add/index.html.twig', [
            'form' => $form,            
            'id' =>$id,
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),            
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);

    }  

    
    #[Route("/dashboard/profile/account/delete/{id}", name:"Delete Bank account", methods:["GET","POST"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');   
        $item = $this->doctrine->getRepository(UserBank::class)->find($id);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/profile/accounts");
    }     
     
}
