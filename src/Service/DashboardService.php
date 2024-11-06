<?php 
 namespace App\Service;

use App\Entity\AppSettings;
use App\Entity\Enum\AccountTypeEnum;
use App\Entity\UserAdmin;
use App\Entity\UserAppNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DashboardService {

    private $em;     
    private $params;
    private $requestStack;

     public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface $params,        
        RequestStack $requestStack
        )
     {  
         $this->em = $em;     
         $this->params = $params;
         $this->requestStack = $requestStack;
     }

     public function getDashboardPermission(?UserAdmin $user){
        $session = $this->requestStack->getSession();        
        $currentPage = $session->get('currentPage');  
        $inprogressRental = true;
        if(empty($currentPage)){
            $inprogressRental = false;
        }
        if($user !=null){
            $settings = $user->getUserSetting();
            return [
                "account"=> $settings->getAccount(),
                "notifications"=> $settings->getNotifications(),
                "rentee"=>strtolower($user->getType()) == strtolower(AccountTypeEnum::RENTEE) ? true:false,
                "newNotificationCount" =>$this->getNotificationCount($user->getNotifications()),
                "inprogressRental" =>$inprogressRental 
             ];
        }
         return  [
            "renter"=>false,
            "anonymous"=> false,
            "account"=> false,
            "rentee"=>false,
            "notifications"=> false,
            "newNotificationCount" => 0,
            "inprogressRental" =>$inprogressRental 
         ];
     }

     public function getNotificationCount($notifications):int{

        $count = 0;
        foreach($notifications as $notification){
            $item = $this->em->getRepository(UserAppNotification::class)->findOneBy(['appNotification'=> $notification]);
          if(!$item->getNotificationRead()){
          $count++;
          }
        }
         return $count;    
     }  

     public function getSiteDetails(){
        $settings = [];
        $appSettings =   $this->em->getRepository(AppSettings::class)->findAll();
        foreach($appSettings as $setting){
            $settings[strtolower($setting->getName())] = $setting->getCustomValue();
        }
        return $settings;
     }
  }