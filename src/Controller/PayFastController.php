<?php

namespace App\Controller;

use App\Entity\UserPayFast;
use App\Form\PayFastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use App\Service\DashboardService;

class PayFastController extends AbstractController
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
#[Route("/dashboard/profile/payfast", name:"User PayFast")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        
        $instance = $user->getUserPayFast(); 
        $formTitle = 'Add Payfast Account';
        
        if (!$instance) {
            $instance = new UserPayFast();
            $formTitle = "Update  Payfast Account";
        }           
        $form = $this->createForm(PayFastType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setClient($user);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(PayFastType::class, $instance);  
            $id = $instance->getId();
        }

        return $this->render('dashboard/payfast/add/index.html.twig', [
            'form' => $form, 
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),            
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);

    }  

    
    #[Route("/dashboard/profile/payfast/delete/{id}", name:"Delete PayFast account", methods:["GET","POST"])]
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');   
        $item = $this->doctrine->getRepository(UserPayFast::class)->find($id);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/profile/payfast");
    }     
     
}
