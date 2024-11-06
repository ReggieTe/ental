<?php

namespace App\Controller;

use App\Service\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DashboardService;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
        private $dashboardServicePermissions;
    private $doctrine;

    private $clientService;

    public function __construct(ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,ClientService $clientService)
    {
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ; 
       $this->clientService = $clientService;    
    }

#[Route("/dashboard", name:"dashboard", methods:["GET","POST"])]
    
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        

        return $this->render('dashboard/home/index.html.twig', [
            'title'=>"Dashboard",
            'items'=>$this->clientService->getDashboadCounts($user),
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    } 
 
}
