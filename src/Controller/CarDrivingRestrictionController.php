<?php

namespace App\Controller;

use App\Entity\UserDrivingRestriction;
use App\Form\Api\CarDrivingRestrictionsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use App\Service\DashboardService;

class CarDrivingRestrictionController extends AbstractController
{    
    private $apiService;  
        private $dashboardServicePermissions;
    private $doctrine;
    

    public function __construct(
        ApiService $apiService,
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions)
    {
        $this->apiService = $apiService;
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ;      
    }
#[Route("dashboard/restrictions", name:"View Restrictions")]
    
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/cars/restriction/index.html.twig', [           
            'title'=>"Additional instructions",
            'list'=> $this->apiService->process($user->getUserDrivingRestrictions()),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

#[Route("dashboard/restriction/add/{id?}", name:"Add or Edit restriction", methods:["GET","POST"])]
    
    public function add(#[CurrentUser] UserAdmin $user,Request $request,?string $id = ''): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = new UserDrivingRestriction(); 
        
        $formTitle = 'Add driving restriction';
        
        if (!empty($id)) {
            $formTitle = "Update driving restriction";
            $instance = $this->doctrine->getRepository(UserDrivingRestriction::class)->find($id);
        }           
        $form = $this->createForm(CarDrivingRestrictionsType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setOwner($user);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(CarDrivingRestrictionsType::class, $instance);  
            $id = $instance->getId();
        }

        return $this->render('dashboard/cars/restriction/add/index.html.twig', [
            'form' => $form,            
            'id' =>$id,
            'title'=>$formTitle, 
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),           
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }

        
    
    #[Route("/dashboard/restriction/delete/{id?}", name:"Delete restriction", methods:["DELETE"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');   
        $item = $this->doctrine->getRepository(UserDrivingRestriction::class)->find($id);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/cars");
    }
     
}
