<?php

namespace App\Controller;

use App\Entity\Enum\AccountTypeEnum;
use App\Entity\UserAdmin;
use App\Entity\UserSetting;
use App\Form\RegistrationProfileFormType;
use App\Service\ApiService;
use App\Service\DashboardService;
use App\Service\Notification;
use App\Service\Validate;
use App\Util\FileSystem;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationUserController extends AbstractController
{
    private $requestStack;
        private $dashboardServicePermissions;
    private $doctrine; 
    private $fileSystem;
    private $notification;
    private $apiService;

    public function __construct(
        RequestStack $requestStack,
        ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,
        FileSystem $fileSystem,
        Notification $notification,
        ApiService $apiService
        )
    {
        $this->requestStack = $requestStack;
        $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; ;   
        $this->fileSystem = $fileSystem;  
        $this->notification = $notification;
        $this->apiService = $apiService;
    }
  
 #[Route("/register", name:"app_register")]
    public function registerProfile(#[CurrentUser] UserAdmin $user,Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new UserAdmin();
        $form = $this->createForm(RegistrationProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();            
            $user->setPassword($userPasswordHasher->hashPassword($user,$password));
            $salt = uniqid();
            $user->setSalt($salt);
            $user->setType(AccountTypeEnum::RENTER);
            $user->setApiPassword(Validate::hash($password,$salt));            
            $entityManager = $this->doctrine->getManager();
            //Create user settings
            $setting = new UserSetting();
            $setting->setAddedby($user);
            //Create user notification

            $entityManager->persist($user);
            $entityManager->persist($setting);
            $entityManager->flush();
            $this->fileSystem->createUserDirectory($user->getId());
            //Send email 
            $appData = $this->dashboardServicePermissions->getSiteDetails(); 
            $this->notification->sendEmail([            
                "from"=>$appData["email"],
                "to"=>$user->getEmail(),
                "replyTo"=>$appData["email"],
                "subject"=>'New account Created',
                "text"=>"Account setup done successfully.",
                "template"=>"email/templates/welcome.html.twig",
                "context"=>[
                    'user' => $user,
                ]            
            ]);

           
        
            $this->notification->sendNotification($user,[
                    'header'=>'Account created',
                    'body'=>'Account setup done successfully.'
                ]);

            $this->addFlash('notice-success',"Account created successfully! Setup account now");

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('AccountSetUp');
        }

        return $this->renderPage ('registration/user/register.profile.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

#[Route("/resetpassword", name:"app_reset_password")]
    public function resetPassword(#[CurrentUser] UserAdmin $user,Request $request): Response
    {    
        $siteDetails = $this->dashboardServicePermissions->getSiteDetails();
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class,['label'=>"Email"])
            ->add('send', SubmitType::class,['label'=>"Reset Password Now",'attr'=>["class"=>"btn btn-bg btn-primary col-md-12"]])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = isset($data['email'])?$data['email']:null;
            if($email!=null){
                $userItems = $this->doctrine->getRepository(UserAdmin::class)->findBy(["email"=>$email]);
                if(count($userItems)){
                    $user = current($userItems);
                    $resetLink = Validate::hash($email,$user->getId());
                    $user->setLastPasswordResetRequestDate(new DateTime('now'));
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    //send rest Password email     
                    $appData = $this->dashboardServicePermissions->getSiteDetails();             
                    $this->notification->sendEmail([            
                        "from"=>$appData["email"],
                        "to"=>$user->getEmail(),
                        "replyTo"=>$appData["email"],
                        "subject"=>'Account Password Reset',
                        "text"=>'Account Password Reset',
                        "template"=>"email/templates/passwordReset.html.twig",
                        "context"=>[
                            'user' => $user,
                            'link'=>$siteDetails['url']."verify/$resetLink",
                        ]
                    ]);
                                
                   $this->notification->sendNotification($user,[
                    'header'=>'Account Password Change Request',
                    'body'=>'Account Password Change Request.Check email for full details!'
                ]);
                    // $this->addFlash('notice-success',"Hi <br> Click <a href='/verify/$resetLink'>here</a> to reset password");
                    $this->addFlash('notice-success',"Check $email for the password reset link");
                }else{
                    $this->addFlash('notice',"No account with email $email found.");
                }
            }            
        }

        return $this->render('registration/user/reset.profile.html.twig', [
            'form' => $form,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission(null),
            'site'=> $siteDetails
        ]);
    }

#[Route("/verify/{id}", name:"app_verify_link_",methods:["GET","POST"])]
    public function verify(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $link = $request->get('id');
        if (isset($link)) {
            $userItems = $this->doctrine->getRepository(UserAdmin::class)->findAll();
            foreach ($userItems as $user) {
                if ($link == Validate::hash($user->getEmail(),$user->getId())) {
                    $d1 = $user->getLastPasswordResetRequestDate();
                    $d2 = new DateTime("now");
                    $interval = $d1->diff($d2);
                    $diffInDays    = $interval->d;
                    if ($diffInDays<=2) {
                        $this->addFlash('notice-success', "Set new password");
                        $session = $this->requestStack->getSession();
                        $session->set('current-user',$user->getId());
                        return $this->redirectToRoute('app_set_password');
                    }else{
                        $this->addFlash('notice', "Link expired .Please reset password again using email");
                        return $this->redirectToRoute('app_reset_password');
                    }
                }
            }
        }
            $this->addFlash('notice', "Invalid link");
            return $this->redirectToRoute('app_reset_password');
        
    }

#[Route("/setpassword", name:"app_set_password")]
    public function setPassword(#[CurrentUser] UserAdmin $user,Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {  
        $form = $this->createFormBuilder()
        ->add('password', PasswordType::class,[ 'constraints' => [
            new NotBlank(),
            new Length(['min' => 8]),
        ],'label'=>"Password",
        "help"=>"Must contain atleast 1 number ,1 letter ,1 special character and minimum lenght of 8 characters"])
        ->add('conpassword', PasswordType::class,['constraints' => [
            new NotBlank(),
            new Length(['min' => 8]),
        ],'label'=>"Confirm Password",
        "help"=>"Must be the same as the password above"
        ])
        ->add('send', SubmitType::class,['label'=>"Reset Password Now",'attr'=>["class"=>"btn btn-bg btn-primary col-md-12"]])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = isset($data['password'])?$data['password']:null;
            $conpassword = isset($data['conpassword'])?$data['conpassword']:0;
            if($password==$conpassword){
                $session = $this->requestStack->getSession();
                $id = $session->get('current-user');
                $user = $this->doctrine->getRepository(UserAdmin::class)->find(["id"=>$id]);
                if($user!=null){
                    $user->setPassword($userPasswordHasher->hashPassword($user,$password));
                    $salt = uniqid();
                    $user->setSalt($salt);
                    $user->setApiPassword(Validate::hash($password,$salt));   
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();  
     
                    $appData = $this->dashboardServicePermissions->getSiteDetails(); 
                    $this->notification->sendEmail([            
                    "from"=>$appData["email"],
                    "to"=>$user->getEmail(),
                    "replyTo"=>$appData["email"],
                    "subject"=>'Account Password Changed',
                    "text"=>'Account Password Changed',
                    "template"=>"email/templates/passwordChanged.html.twig",
                    "context"=>[
                        'user' => $user
                    ]
                    ]);

                   $this->notification->sendNotification($user,[
                    'header'=>'Account Password Changed',
                    'body'=>'Account Password Changed successfully.Check email for full details!'
                ]);

                    $this->addFlash('notice-success', "Password changed successfully.Login");
                    return $this->redirectToRoute('app_login');
                }else{
                    $this->addFlash('notice',"Invalid session");
                }
            }else{
                $this->addFlash('notice',"Password must match.Please check and try again");
            }            
        }

        return $this->render('registration/user/new.profile.html.twig', [
            'form' => $form,
            'permissions'=>$this->dashboardServicePermissions->getDashboardPermission(null),
            'site'=> $this->dashboardServicePermissions->getSiteDetails()
        ]);
    }

    private function renderPage (String $template,array $data = []):Response 
    {
        $data['permissions'] = $this->dashboardServicePermissions->getDashboardPermission(null);
        $data['site'] = $this->dashboardServicePermissions->getSiteDetails();
        return $this->render($template,$data);

    }
}
