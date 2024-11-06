<?php

namespace App\Controller;

use App\Entity\Levy;
use App\Entity\UserBank;
use App\Entity\UserLevy;
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
use App\Service\Validate;

class LevyController extends AbstractController
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
#[Route("/dashboard/profile/levies", name:"User levies")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $remainLevies = [];
        $userActiveLives = [];
        $userLevies =  $user->getLevies();
        $levies = $this->doctrine->getRepository(Levy::class)->findBy(['active'=>1,'mandatory'=>0]);
        
        $mandatory = $this->doctrine->getRepository(Levy::class)->findBy(['active'=>1,'mandatory'=>1]);
       // dd($userLevies);
        foreach($levies as $levy){
            $found = false;
            foreach($userLevies as $userLevy){
                if($userLevy->getId()==$levy->getId()){
                    $found = true;
                }
            }
            if(!$found){
                array_push($remainLevies,$levy);
            }
        }

        $userActiveLives = Validate::array_merge([$mandatory,$userLevies]); 

        return $this->render('dashboard/levy/index.html.twig', [           
            'title'=>"Levies",
            'list'=> $this->apiService->process($userActiveLives),
            'levies'=> $this->apiService->process($remainLevies),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    
    }
    
    #[Route("/dashboard/profile/levy/add/{id?}", name:"AddNewLevy", methods:["GET","POST"])]
    public function saveBank(#[CurrentUser] UserAdmin $user,Request $request,$id): Response
    {
       
        
        $levy = $request->request->get('levy');      
        if ($levy) {
            $instance = new UserLevy();
            $entityManager = $this->doctrine->getManager();
            $instance->setUserAdmin($user->getId());
            $instance->setLevy($levy);
            $instance->setActive(true);
            $entityManager->persist($instance);
            $entityManager->flush();  
            $this->addFlash('success', 'Item added  successfully');
        }else{
            $this->addFlash('danger', "Failed to add item");
        }   

        return $this->redirect("/dashboard/profile/levies");

    }  

    
    #[Route("/dashboard/profile/levy/delete/{id}", name:"Delete levy levy", methods:["GET","POST"])]
    public function delete(#[CurrentUser] UserAdmin $user,Request $request,$id) {     
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
          
        $item = $this->doctrine->getRepository(UserLevy::class)->findOneBy(['levy'=>$id,'user_admin'=>$user->getId()]);
        if($item){
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', "Item deleted successfully");
            }else{
                $this->addFlash('danger', "Failed to delete item");
            }        
        return $this->redirect("/dashboard/profile/levies");
    }     
     
}
