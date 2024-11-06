<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
        private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions)
    {
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ;     
    }
#[Route("/_error/500", name:"Internal Server Error")]
    
    public function internalServerError(): Response
    {
        return $this->renderPage('error/500/index.html.twig',[
            'title'=>'Internal Server Error'
        ]);
    }

#[Route("/_error/404", name:"Page Not Found")]
    
    public function pageNotFound(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        return $this->renderPage('error/404/index.html.twig',[
            'title'=>'Page Not Found'
        ]);
    }

   
    private function renderPage (String $template,array $data = []):Response 
    {
        $data['permissions'] = $this->dashboardServicePermissions->getDashboardPermission(null);
        $data['site'] = $this->dashboardServicePermissions->getSiteDetails();
        return $this->render($template,$data);
    }
}
