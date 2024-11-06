<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginUserController extends AbstractController
{
    private $dashboardServicePermissions;

public function __construct(
    DashboardService $dashboardServicePermissions)
{
    $this->dashboardServicePermissions = $dashboardServicePermissions;
}
    #[Route("/login", name:"app_login")]
    public function profile(Request $request,AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('products');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $message = $request->get('message');
        return $this->renderPage ('security/login.profile.html.twig',
         [
         'error' => $error,
         'title'=>"Sign in",
         'message'=>$message,
         'permissions'=>$this->dashboardServicePermissions->getDashboardPermission(null),
         'site'=> $this->dashboardServicePermissions->getSiteDetails(),
        ]);
    }

    #[Route("/logout", name:"app_logout", methods:["GET","POST"])]
    public function logoutApp(Request $request)
    { 
        $request->getSession()->invalidate();
        return $this->redirectToRoute('app_login',['message' =>"Log out successfully"]);
    }
    
    private function renderPage (String $template,array $data = []):Response 
    {
        return $this->render($template,$data);

    }
}

