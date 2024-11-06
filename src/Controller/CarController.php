<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Common;
use App\Service\DashboardService;

class CarController extends AbstractController
{
    private $common;
        private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(
        Common $common,
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions)
    {
        $this->common = $common; 
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ;      
    }
#[Route("dashboard/cars", name:"View Cars")]
    
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/cars/index.html.twig', [           
            'title'=>"Cars",
            'list'=> $user->getCars(),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

#[Route("dashboard/car/add/{id?}", name:"Add or Edit Car", methods:["GET","POST"])]
    
    public function add(#[CurrentUser] UserAdmin $user,Request $request,?string $id = ''): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $instance = new Car(); 
        
        $formTitle = 'Add car';
        $images = [];
        
        if (!empty($id)) {
            $formTitle = "Update car";
            $instance = $this->doctrine->getRepository(Car::class)->find($id);
            $images = $this->common->getFiles($id,$user->getId(),'car');
        }           
        $form = $this->createForm(CarType::class, $instance);    
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $instance->setOwner($user);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', $formTitle.' was successfully');
            $form = $this->createForm(CarType::class, $instance);  
            $id = $instance->getId();
        }

        return $this->render('dashboard/cars/add/index.html.twig', [
            'form' => $form,            
            'id' =>$id,
            'owner'=>$user->getId(),
            'images' =>$images,
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),            
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }

        
    
    #[Route("/dashboard/car/delete/{id}", name:"Delete Car", methods:["GET","POST"])]
    
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');   
        $item = $this->doctrine->getRepository(Car::class)->find($id);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->common->deleteMultipleFiles($id,$user->getId(),"car");
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/cars");
    }
     
     
}
