<?php

namespace App\Controller;

use App\Entity\UserSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SettingType;
use App\Service\DashboardService;

class SettingController extends AbstractController
{
        private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions)
    {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ;      
    }
#[Route("dashboard/setting", name:"User Setting")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $admin = new UserSetting();; 
        
        $id = $user->getId();
        $formTitle = 'Settings';

        $admin = $this->doctrine->getRepository(UserSetting::class)->findBy(['addedby'=>$id]);
        if(count($admin)){
            $admin = current($admin);
        }

        $form = $this->createForm(SettingType::class, $admin);    
        $form->handleRequest($request);   
        //$personMissing = $form->getData()->setAddedby($user);     
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();    
            $this->addFlash('success', $formTitle.' was updated successfully');
        }

        return $this->render('dashboard/setting/index.html.twig', [
            'form' => $form,
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }     
}
