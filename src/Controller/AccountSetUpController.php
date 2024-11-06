<?php

namespace App\Controller;


use App\Entity\UserSetting;
use App\Form\AccountSetupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\DashboardService;

class AccountSetUpController extends AbstractController
{
        private $dashboardServicePermissions;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions)
    {
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine;        
    }
#[Route("dashboard/account-setup", name:"AccountSetUp")]
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

        $form = $this->createForm(AccountSetupType::class, $admin);    
        $form->handleRequest($request);       
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();    
            $this->addFlash('success', 'Add required documents to be able to rent a car');

            return $this->redirectToRoute('UploadDocuments'); 
        }

        return $this->render('dashboard/accountsetup/index.html.twig', [
            'form' => $form,
            'title'=>$formTitle,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission($user),
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }     
}
