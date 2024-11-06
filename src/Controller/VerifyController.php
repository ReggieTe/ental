<?php

namespace App\Controller;

use App\Entity\UserOtp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserAdmin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\DashboardService;
use App\Service\Notification;
use DateTime;
use Exception;
use Symfony\Component\Mailer\MailerInterface;

class VerifyController extends AbstractController
{
        private $dashboardServicePermissions;
    private $doctrine;
    
    private $notification;

    public function __construct(ManagerRegistry $doctrine,DashboardService $dashboardServicePermissions,Notification $notification)
    {
       $this->dashboardServicePermissions = $dashboardServicePermissions;
        $this->doctrine = $doctrine; 
       $this->notification = $notification;     
    }
#[Route("/dashboard/verify/phone", name:"Verify Phone")]
    public function index(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $phone = $user->getPhone();
        $message = "Phone required.Please try again";
        $appData = $this->dashboardServicePermissions->getSiteDetails();
        if ($phone) {
            $codeSent = false;
            $createNewOtp = false;
            $otpCode = rand(1001, 9999);
            //check previously sent otp
            $d2 = new DateTime('now');
            $entityManager = $this->doctrine->getManager();
            $otp = $this->doctrine->getRepository(UserOtp::class)->findOneBy(['phone'=>$phone]);
            if($otp){
                $datecreated = $otp->getDateCreated();
                $interval = $datecreated->diff($d2);
                $diffInMinutes    = $interval->i;
                if ($diffInMinutes<5) {
                    $otpCode = $otp->getOtp();
                    $message = "OTP sent try again after 5 minutes.Due to network issue it might take time";
                }else{
                    $entityManager->remove($otp);
                    $entityManager->flush();
                    $createNewOtp = true;
                    $codeSent = true;
                }
            }else {
                $createNewOtp = true;
                $codeSent = true;
            }             
            if($createNewOtp) {
                    $otp = new UserOtp();
                    $otp->setOtp($otpCode);
                    $otp->setPhone($phone);
                    $otp->setAddedby($user);
                    $entityManager->persist($otp);
                    $entityManager->flush();
                }

            if($codeSent){
                $response = $this->notification->sendSms(['phone'=>$phone ,"message"=> $appData['name']. " verification code ".$otpCode]);
                if($response['code']){
                 $message = "Verification code sent to Phone number";
                 $codeSent = true;
                }else{
                 $message = "Failed to send code.Please try again later";
                }
            }
        }

        $this->addFlash('warning', $message);
        return $this->redirect("/dashboard/profile");
    }

#[Route("/dashboard/verify/phone/otp", name:"Verify_Phone_Otp")]
    public function verifyOtp(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            
            $phone = $user->getPhone();
            $digit1 = $request->get('digit-1');
            $digit2 = $request->get('digit-2');
            $digit3 = $request->get('digit-3');
            $digit4 = $request->get('digit-4');
            $otp = $digit1.$digit2.$digit3.$digit4;
                      
            if (isset($phone)&&!empty($otp)) {
                $otps = $this->doctrine->getRepository(UserOtp::class)->findBy(['phone'=>$phone,'state'=>0,'otp'=>$otp]);
                if (count($otps)) {
                    $foundOtp = current($otps);
                    $user->setPhoneVerified(1);
                    $user->setDateModified(new DateTime('now'));
                    $foundOtp->setState(1);
                    $foundOtp->setDateModified(new DateTime('now'));
    
                    $entityManager = $this->doctrine->getManager();
                    $entityManager->persist($user);
                    $entityManager->persist($foundOtp);
                    $entityManager->flush();
    
                    $this->addFlash("success", "Phone number verified successfully"); 
                    $this->notification->sendNotification($user,[
                        'header'=>"Phone number verified successfully",
                        'body'=>"Phone number verified successfully! You're now able to receive sms notifications"
                    ]);

                }else{
                    $this->addFlash("warning", "Otp expired, request a new otp!");
                }                
            }else{
                $this->addFlash("danger", "Failed to verify phone number.Try again.");
            }            
        } catch (Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        }
        return $this->redirect("/dashboard/profile");
    }

#[Route("/dashboard/verify/email", name:"Verify Email")]
    public function verifyEmail(MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $email = $user->getEmail();
        $message = "Email required.Please try again";
        if (isset($email)) {
            $codeSent = false;
            $otpCode = rand(1000, 10000);
            //check previously sent otp
            $d2 = new DateTime('now');
            $entityManager = $this->doctrine->getManager();
            $otp = $this->doctrine->getRepository(UserOtp::class)->findOneBy(['email'=>$email]);
            if($otp){
                $datecreated = $otp->getDateCreated();
                $interval = $datecreated->diff($d2);
                $diffInMinutes    = $interval->i;
                if ($diffInMinutes<5) {
                    $otpCode = $otp->getOtp();
                    $message = "OTP sent try again after 5 minutes.Due to network issue it might take time";
                }else{
                    $entityManager->remove($otp);
                    $entityManager->flush();
                    $createNewOtp = true;
                    $codeSent = true;
                }
            }else {
                $createNewOtp = true;
                $codeSent = true;
            }             
            if($createNewOtp) {
                    $otp = new UserOtp();
                    $otp->setOtp($otpCode);
                    $otp->setEmail($email);
                    $otp->setAddedby($user);
                    $entityManager->persist($otp);
                    $entityManager->flush();
                }

            if($codeSent){
                $appData = $this->dashboardServicePermissions->getSiteDetails();             
                $this->notification->sendEmail([            
                "from"=>$appData["email"],
                "to"=>$user->getEmail(),
                "replyTo"=>$appData["email"],
                "subject"=>"Verification Code",
                "text"=>"Verification Code",
                "template"=>"email/templates/otp.html.twig",
                "context"=>[
                    'user' => $user,
                    'code'=>$otpCode,
                ]
                ]);
            $message = "Verification code sent to email";
            $codeSent = true;
            }
        }
        $this->addFlash('warning', $message);
        
        return $this->redirect("/dashboard/profile");
    }

#[Route("/dashboard/verify/email/otp", name:"Verify_Email_Otp")]
    public function verifyEmailOtp(#[CurrentUser] UserAdmin $user,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try{ 
            
            $email = $user->getEmail();
            $digit1 = $request->get('digit-1');
            $digit2 = $request->get('digit-2');
            $digit3 = $request->get('digit-3');
            $digit4 = $request->get('digit-4');
            $otp = $digit1.$digit2.$digit3.$digit4;                      
            if (isset($email)&&!empty($otp)) {                
                    $otps = $this->doctrine->getRepository(UserOtp::class)->findBy(['email'=>$email,'state'=>0,'otp'=>$otp]);
                    if (count($otps)) {
                        $foundOtp = current($otps);
                        $user->setEmailVerified(1);
                        $user->setDateModified(new DateTime('now'));
                        $foundOtp->setState(1);
                        $foundOtp->setDateModified(new DateTime('now'));    
                        $entityManager = $this->doctrine->getManager();
                        $entityManager->persist($user);
                        $entityManager->persist($foundOtp);
                        $entityManager->flush();    
                        
                    $this->addFlash("success", "Email verified successfully"); 
                    $this->notification->sendNotification($user,[
                        'header'=>"Email verified successfully",
                        'body'=>"Email verified successfully ! You're now able to receive email notifications"
                    ]);
                }else{
                    $this->addFlash("warning", "Otp expired, request a new otp!");
                }                
            }else{
                $this->addFlash("danger", "Failed to verify email.Try again.");
            }
        }catch(Exception $e){
            $this->addFlash("danger",$e->getMessage());
       }
       return $this->redirect("/dashboard/profile");
    }       

     
}
