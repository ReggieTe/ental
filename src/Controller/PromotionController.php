<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Form\PromotionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use App\Service\DashboardService;

class PromotionController extends AbstractController
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
#[Route("/dashboard/profile/promotions", name:"User Promotions")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/promotion/index.html.twig', [           
            'title'=>"Promotions",
            'list'=> $this->apiService->process($user->getPromotions()),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    
    }
    
    #[Route("/dashboard/profile/promotion/add/{id?}", name:"Save Promotion Promotions", methods:["GET","POST"])]
    public function saveBank(#[CurrentUser] UserAdmin $user,Request $request,$id): Response
    {
        $instance = new Promotion();
        
        $formTitle = 'Add Promotion';
        
        if ($id) {
            $item = $this->doctrine->getRepository(Promotion::class)->find($id);
            if($item){
                
            $formTitle = "Update promotion";
                $instance = $item;
            }
        }           
        $form = $this->createForm(PromotionType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setOwner($user);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(PromotionType::class, $instance);  
            $id = $instance->getId();
        }

        return $this->render('dashboard/promotion/add/index.html.twig', [
            'form' => $form,            
            'id' =>$id,
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),            
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);

    }  

    
    #[Route("/dashboard/profile/promotion/delete/{id}", name:"Delete Promotion promotion", methods:["GET","POST"])]
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');   
        $item = $this->doctrine->getRepository(Promotion::class)->find($id);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/profile/promotions");
    }     
     
}
