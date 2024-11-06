<?php 
 namespace App\Service;

use App\Entity\AppUserNotification;
use App\Entity\UserAdmin;
use App\Entity\UserAppNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twilio\Rest\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Notification {

    private $em;

    private $mailer;

    private $params;

    private $dashboardServicePermissions;

    public function __construct(
        DashboardService $dashboardServicePermissions,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        ParameterBagInterface $params)
     {
         $this->em = $em;     
         $this->mailer = $mailer;
         $this->params = $params;
         $this->dashboardServicePermissions = $dashboardServicePermissions;  
     }

     //send local notification 
     public function sendNotification(UserAdmin $user,$data =[
            'header'=>"Welcome , We glad you could join us",
            'body'=>"Thank you for becoming part of the big community"
     ]){
         $sendNotification = true;
                //send notification                  
                    $notificationKeys = ['header','body'];
                    if (count($notificationKeys)==count($data)) {
                        foreach ($data as $key => $value) {
                            if (empty($value)) {
                                $sendNotification = false;
                            }
                        }
                        if ($sendNotification) {                            
                            //create notification
                            $notification = new AppUserNotification();
                            $notification->setHead($data['header']);
                            $notification->setBody($data['body']);
                            $this->em->persist($notification);
                            $this->em->flush();
                            //create notification Notification
                            $notificationNofitication = new UserAppNotification();
                            $notificationNofitication->setAppNotification($notification->getId());
                            $notificationNofitication->setUser($user->getId());
                            $notificationNofitication->setNotificationRead(false);
                            $this->em->persist($notificationNofitication);
                            $this->em->flush();
                           // Send notification to phone                           
                       }
                    }
               
     }

     //send local notification 
     public function sendEmail($data =[
            "from"=>"hello@jiriapp.com",
            "to"=>"hello@jiriapp.com",
            "replyTo"=>"no-reply@jiriapp.com",
            "subject"=>"Account Password Reset",
            "text"=>"Thank you for creating an Account on App.",
            "template"=>"email/templates/defualt.html.twig",
            "context"=>[]
     ],$documents = [],$generateFiles = []){
         $sendEmail = true;
            //Create user notification                
                //send email                
                    $emailKeys = ["from","to","replyTo","subject","text","template","context"];
                    if (count($emailKeys)==count($data)) {
                        foreach ($data as $key => $value) {
                            if (empty($value)) {
                                $sendEmail = false;
                            }
                        }
                        if ($sendEmail) {
                            try{
                                $appData = $this->dashboardServicePermissions->getSiteDetails();    
                                $context = (array)$data["context"];
                                $context['site'] = $appData;
                                $emailObj = (new TemplatedEmail())
                                ->from(new Address($data["from"], $appData['name']))
                                ->to($data["to"])
                                ->replyTo($data["replyTo"])
                                ->subject($data["subject"])
                                ->text($data["subject"])
                                ->htmlTemplate($data["template"])->context($context);    
                                
                                if($documents) {
                                    foreach($documents as $document) {
                                        $emailObj = $emailObj->attach(fopen($document['file'], 'r'), $document['type'], 'application/pdf');
                                    }
                                }

                                if($generateFiles) {
                                    foreach($generateFiles as $document) {
                                        $emailObj = $emailObj->attach($document['contents'], $document['name'], 'application/pdf');
                                    }
                                }

                                $this->mailer->send($emailObj);
                            }catch(\Exception $e){

                            }
                            
                        }
                    }         
               
     }   
     
     //send local notification 
     public function sendSmsWithNoUser($data =[
           'phone'=>"+27610549027",
            'message'=>'Test Message Notification'
        ]){
         $sendSms = true;
            //Create user notification
                //send sms
                    $smsKeys = ['phone','message'];  
                    if (count($smsKeys)==count($data)) {
                        foreach ($data as $key => $value) {
                            if (empty($value)) {
                                $sendEmail = false;
                            }
                        }
                        if ($sendSms) {
                            return $this->smsWithNoUser($data['phone'], $data['message']);
                        }
                    }
          return false;     
     }
     //send local notification 
     public function sendSms($data =[
           'phone'=>"+27610549027",
            'message'=>'Test Message Notification'
        ]){
         $sendSms = true;
            //Create user notification
                //send sms
                    $smsKeys = ['phone','message'];  
                    if (count($smsKeys)==count($data)) {
                        foreach ($data as $key => $value) {
                            if (empty($value)) {
                                $sendEmail = false;
                            }
                        }
                        if ($sendSms) {
                            return $this->send($data['phone'], $data['message']);
                        }
                    }
          return false;     
     }

     public function fcmMessages(array $registrationIds,array $msg)
     {
         try {
                   $API_ACCESS_KEY = $this->params->get('FCM_API_ACCESS_KEY');           
                   $response = ['error'=>true];
                         if(count($registrationIds)==0){
                             return $response['message'] = "Atleast one device id is required";
                         }
                         if(!isset($msg['title'])){
                             return $response['title'] = "Notification title is required";
                         }
                         if(!isset($msg['subtitle'])){
                             return $response['subtitle'] = "Notification subtitle is required";
                         }
                         if(!isset($msg['message'])){
                             return $response['message'] = "Notification message is required";
                         }
            
                         // prep the bundle
                         $msg = array
                         (
                             'message' 	=> $msg['message'],
                             'title'		=> $msg['title'],
                             'subtitle'	=> $msg['subtitle'],
                             'tickerText'	=> 'Finguard Notifications',
                             'vibrate'	=> 1,
                             'sound'		=> 1,
                             'largeIcon'	=> 'large_icon',
                             'smallIcon'	=> 'small_icon'
                         );
                         
                         $fields = [
                             'registration_ids' => $registrationIds,
                             'data'			=> $msg,
                             'notification'			=> $msg
                         ];
                         
                         $headers = [
                             'Authorization: key=' . $API_ACCESS_KEY,
                             'Content-Type: application/json'
                         ];
            
                        $ch = curl_init();
                        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                        curl_setopt( $ch,CURLOPT_POST, true );
                        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                        $result = curl_exec($ch );
                        curl_close( $ch );
 
                        $response['error'] = false;
                        $response['message'] = "Request successful";
                        $response['data'] = $result;
 
                        return $response;
                        
                    }catch(\Exception $e){
                        return $response['message'] = $e->getMessage();
                    }
     }

     
    public function smsWithNoUser( $phone ,$message)
    {
        try{       
        $accountSid = $this->params->get('TWILIO_ACCOUNT_SID');
        $authToken = $this->params->get('TWILIO_AUTH_TOKEN');
        $twilioNumber = $this->params->get('TWILIO_NUMBER');

        if($phone==""){
            return ['code'=>false,"message"=>"Phone required.Please try again"]; 
        }
        if($message==""){
            return ['code'=>false,"message"=>"Message body required.Please try again"]; 
        }

        if (isset($phone)) {
                $client = new Client($accountSid, $authToken);
                $client->messages->create(
                    $phone,
                    array(
                        'from' => $twilioNumber,
                        'body' => $message
                    )
                );
        }
        return ['code'=>false,"message"=>"Failed to send message.Try again later"];
       }catch(\Exception $e){
        return ['code'=>400,"message"=>$e->getMessage()];
       }
    }
    public function send( $phone ,$message)
    {
        try{       
            
        $accountSid = $this->params->get('TWILIO_ACCOUNT_SID');
        $authToken = $this->params->get('TWILIO_AUTH_TOKEN');
        $twilioNumber = $this->params->get('TWILIO_NUMBER');

        if($phone==""){
            return ['code'=>false,"message"=>"Phone required.Please try again"]; 
        }
        if($message==""){
            return ['code'=>false,"message"=>"Message body required.Please try again"]; 
        }

        if (isset($phone)) {
            $users = $this->em->getRepository(UserAdmin::class)->findBy(['phone'=>$phone]);
            if (count($users)) {
                $user = current($users);
                $cleanPhone = Validate::sortPhone($user->getPhone());
                $userNumber = "+27".$cleanPhone;
                $client = new Client($accountSid, $authToken);
                $client->messages->create(
                    $userNumber,
                    array(
                        'from' => $twilioNumber,
                        'body' => $message
                    )
                );
                return ["code"=>true,"message"=>"Message sent"];
            }
        }
        return ['code'=>false,"message"=>"Failed to send message.Try again later"];
       }catch(\Exception $e){
        return ['code'=>400,"message"=>$e->getMessage()];
       }
    }

 
  }