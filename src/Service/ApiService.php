<?php 
 namespace App\Service;

use App\Entity\AppSettings;
use App\Entity\AppUserNotification;
use App\Entity\Car;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\Enum\PaymentEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\InformationCenter;
use App\Entity\Levy;
use App\Entity\Promotion;
use App\Entity\Rental;
use App\Entity\RentalDiscount;
use App\Entity\RentalLevy;
use App\Entity\UserAdmin;
use App\Entity\UserAppNotification;
use App\Entity\UserBank;
use App\Entity\UserCarAdditional;
use App\Entity\UserCarAvailableItem;
use App\Entity\UserCarIssue;
use App\Entity\UserCarMissingItem;
use App\Entity\UserDrivingRestriction;
use App\Entity\UserLevy;
use App\Entity\UserPayFast;
use App\Entity\UserPayPal;
use App\Entity\UserSetting;
use App\Entity\UserTripChecklist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiService {
     private $em;
     private $common;
     private $dashboardService;
     private $params;

     public function __construct(EntityManagerInterface $em,Common $common,DashboardService $dashboardService,ParameterBagInterface $params)
     {
         $this->em = $em;    
         $this->common = $common;
         $this->dashboardService = $dashboardService;
         $this->params = $params;
     }

    public function prepareFilters($vehicles,$type){
        $sort = [];        
        $app = $this->dashboardService->getSiteDetails();
        if(!count($vehicles)){
            $vehicles = $this->em->getRepository(Car::class)->findBy(['active'=>1,'booked'=>0]);
        }
        foreach($vehicles as $vehicle){
            if($type =='fuel'){
                if(!empty($vehicle->getFuel())){
                $sort[$vehicle->getFuel()]= $vehicle->getFuel();
            }
            }
            if($type =='transmission'){
                if(!empty($vehicle->getTransmission())){
                $sort[$vehicle->getTransmission()]= $vehicle->getTransmission();
            }
            }
            if($type =='brand'){
                if(!empty($vehicle->getBrand())){
                $sort[$vehicle->getBrand()]= $vehicle->getBrand();
            }
            }
            if($type =='price'){
                if(!empty($vehicle->getRatePerDay())){
                $sort[$vehicle->getRatePerDay()] = $app['currency'].$vehicle->getRatePerDay();
            }
            }
            if($type =='deposit'){
                if(!empty($vehicle->getRefundableDeposit())){
                $sort[$vehicle->getRefundableDeposit()] = $app['currency'].$vehicle->getRefundableDeposit();
            }
            }            
            if($type =='payment'){
                if($vehicle->getOwner()->getIsBankEnabled()){
                    $sort[PaymentEnum::EFT]=PaymentEnum::EFT;
                }
                if($vehicle->getOwner()->getIsPaypalEnabled()){
                    $sort[PaymentEnum::PAYPAL]=PaymentEnum::PAYPAL;
                }
                if($vehicle->getOwner()->getIsPayfastEnabled()){
                    $sort[PaymentEnum::PAYFAST] = PaymentEnum::PAYFAST;
                }
            }
        }
        return $sort;
    }
    public function validateToken($token){
        if ($token!=null) {
            $tokenHolders = $this->em->getRepository(UserAdmin::class)->findBy(['token'=>$token]);
            if (count($tokenHolders)) {
                $user = current($tokenHolders);                
                if($user instanceof UserAdmin){
                   return['object'=>$user,'json'=> $this->sortObjectToArray($user)];
                }
            }
        }
        return null;
    }
    public function process($items=[]){
         $processedItems = [];
            foreach ($items as $item) {
                $processedItem = $this->sortObjectToArray($item);
                if($processedItem!=null){
                    array_push($processedItems,$processedItem);
                }
            }
        return $processedItems;
     }
    public function processEnum($items=[],$flip=true){
        $processedItems = [];
            if($flip){
            $items = array_flip($items);
            }
           foreach ($items as $key => $value) {
               $processedItem = ['id'=>$key,'value'=>$value];
               if($processedItem!=null){
                   array_push($processedItems,$processedItem);
               }
           }
       return $processedItems;
    }
    public function sortObjectToArray($object):array{ 
        
        if($object instanceof Promotion){
            return[	
                 'id'=>$object->getId(),  
                 'name'=>$object->getName(),                
                 'description'=>$object->getDescription(),                
                 'type'=>$object->getType(),
                 'amount'=>$object->getAmount(),  
                 'startDate'=>date_format($object->getStartDate(),'j F Y h:m'),
                 'endDate'=>date_format($object->getEndDate(),'j F Y h:m'),
                 'active'=>$object->getActive(),
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                ];
         }

        if($object instanceof Levy){
            return[	
                 'id'=>$object->getId(),  
                 'name'=>$object->getName(),                
                 'description'=>$object->getDescription(),
                 'amount'=>$object->getAmount(),  
                 'mandatory'=>$object->getMandatory(),
                 'active'=>$object->getActive(),
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                ];
         } 
        
        if($object instanceof UserDrivingRestriction){
            return[	
                 'id'=>$object->getId(),                  
                 'description'=>$object->getDescription(),
                 'fine'=>$object->getFine(),  
                 'state'=>$object->getState(),
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                ];
         } 
            
        if($object instanceof UserCarAdditional){
            return[	
                 'id'=>$object->getId(),                  
                 'description'=>$object->getDescription(),
                 'amount'=>$object->getAmount(),
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                 'state'=>$object->getState()
                ];
         } 

        if($object instanceof UserCarAvailableItem){
           return[	
                'id'=>$object->getId(),                  
                'description'=>$object->getDescription(), 
                'amount'=>$object->getAmount(),
                'measurement'=>$object->getMeasurement(),
                'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
               ];
           } 
        if($object instanceof UserCarMissingItem ){
            return[	
                 'id'=>$object->getId(),                  
                 'description'=>$object->getDescription(), 
                 'amount'=>$object->getAmount(),
                 'measurement'=>$object->getMeasurement(),
                 'total'=>(float)$object->getAmount()*(int)$object->getMeasurement(),
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                ];
            }            
        if($object instanceof UserCarIssue){
            return[	
                 'id'=>$object->getId(),                  
                 'description'=>$object->getDescription(), 
                 'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                ];
         } 
        if($object instanceof UserTripChecklist){
            return[	
                'id'=>$object->getId(),                  
                'description'=>$object->getDamageDescription(), 
                'date'=>date_format($object->getDateCreated(),'j F Y h:m'), 
                'type'=> array_flip(ChecklistEnum::choices())[$object->getType()],
                'rental'=>$object->getRental()->getId(),
                'car'=>$this->sortObjectToArray($object->getRental()->getCar()),
                'milleage'=>$object->getMilleage(),
                'ownerAgreed'=>$object->getOwnerAgreed(),             
                'renterAgreed'=>$object->getRenterAgreed(),
                'done'=> $object->getRenterAgreed()&&$object->getOwnerAgreed()?true:false,
                'fuelAvailable'=>$object->getFuelAvailable(),
                'dateSignedByOwner'=> $object->getDateSignedByOwner()?date_format($object->getDateSignedByOwner(),'j F Y h:m'):"", 
                'dateSignedByRenter'=>$object->getDateSignedByRenter()?date_format($object->getDateSignedByRenter(),'j F Y h:m'):"", 
                'userCarIssues'=>$this->process($object->getUserCarIssues()),
                'userCarAvailableItems'=>$this->process($object->getUserCarAvailableItems()),
                'userCarMissingItems'=>$this->process($object->getUserCarMissingItems()),
                'status'=>ucfirst($object->getStatus())
                ];
            } 
        if($object instanceof Rental){
            $efts = $this->common->getFiles($object->getId(),$object->getUser()->getId(),"eft");
            $downloadPayment ="";
            if(count($efts)){
                $eft = current($efts);
                $downloadPayment = $eft['file'];
            }
            return[	
                 'id'=>$object->getId(),                  
                 'car'=>$this->sortObjectToArray($object->getCar()), 
                 'client'=>$this->sortObjectToArray($object->getUser()), 
                 'location'=>$object->getLocation(),
                 'dlocation'=>$object->getDropOffLocation(),
                 'dropoffdate'=>date_format($object->getDropoffdate(),'j F Y h:m a'), 
                 'pickupdate'=>date_format($object->getPickupdate(),'j F Y h:m a'),
                 'amount'=>$object->getQuoteAmount(), 
                 'paymentStatus'=>$object->getPaymentStatus(),
                 'levy'=>$this->process($object->getLevies()),
                 'status'=>$object->getStatus(),
                 'paymentTye'=>$object->getPaymentType(),
                 'dateCreated'=>date_format($object->getDateCreated(),'j F Y h:m'),
                 'dateModified'=>date_format($object->getDateModified(),'j F Y h:m'),
                 'completePayment'=> $object->getPaymentStatus()==PaymentStatusEnum::PENDING ? true : false ,
                 'approved'=> $object->getPaymentStatus()==PaymentStatusEnum::SUCCESSFUL ? true : false ,
                 'checklists'=>$this->process($object->getUserTripChecklists()),
                 'downloadPayment'=>$downloadPayment,
                ];
         }
        if($object instanceof UserAdmin){
             return[	
                'id'=>Validate::null($object->getId()),
                'token'=>Validate::null($object->getToken()),
                'fcm'=>Validate::null($object->getFcmId()),  
                'name'=>Validate::null($object->getName()), 
                'fullname'=>Validate::null($object->getFullName()),
                'address'=>Validate::null($object->getAddress()), 
                'phone'=>Validate::null($object->getPhone()),
                'isPhoneVerified'=>$object->getPhoneVerified(),                        
                'email'=>Validate::null($object->getEmail()), 
                'isEmailVerified'=>$object->getEmailVerified(), 
                'location'=>Validate::null($object->getLocation()), 
                'website'=>Validate::null($object->getWebsite()),
                'isPaypalEnabled'=>$object->getIsPaypalEnabled(),
                'isPayfastEnabled'=>$object->getIsPayfastEnabled(),
                'isBankEnabled'=>$object->getIsBankEnabled()
                 ];
          }

        if($object instanceof Car){
            return [
                'id'=>$object->getId(),
                'owner'=>$this->sortObjectToArray($object->getOwner()),
                'booked'=>$object->getBooked(),
                'state'=>$object->getActive(),
                'name'=>$object->getName(),
                'description'=>$object->getDescription(),
                'doors'=>$object->getDoorNumber(),
                'seats'=>$object->getSeatNumber(),
                'bags'=>$object->getBagNumber(),
                'transmission'=>ucfirst($object->getTransmission()),
                'fuel'=>ucfirst($object->getFuel()),
                'brand'=>$object->getBrand(),
                'aircon'=>$object->getAircon(),
                'leatherUpholstery'=>$object->getLeatherUpholstery(),
                'gps'=>$object->getGps(),
                'rate'=>$object->getRatePerDay(),
                'deposit'=>$object->getRefundableDeposit(),
                'images'=>$this->common->getFiles($object->getId(),$object->getOwner()->getId(),'car')
                ];

         } 

        if($object instanceof UserPayPal){
            return [
                'id'=>$object->getId(),
                'merchantEmail'=>$object->getMerchantEmail(),
                'date'=>date_format($object->getDateCreated(),"j F Y"),
             ];
         } 

        if($object instanceof UserPayFast){
            return [
                'id'=>$object->getId(),
                'merchantId'=>$object->getMerchantId(),
                'merchantKey'=>$object->getMerchantKey(),
                'date'=>date_format($object->getDateCreated(),"j F Y"),
             ];
         }  

        if($object instanceof UserBank){
            return [
                'id'=>$object->getId(),
                'holder'=>$object->getAccountHolder(),
                'account'=>$object->getAccountNumber(),
                'type'=>$object->getAccountType(),
                'bank'=>$object->getAccountBank(),
                'code'=>$object->getAccountBranchCode(),
                'default'=>$object->getDefaultAccount(),
                'date'=>date_format($object->getDateCreated(),"j F Y"),
             ];
         }  

        if($object instanceof AppUserNotification){
            return [
                'id'=>$object->getId(),
                'head'=>$object->getHead(),
                'body'=>$object->getBody(),
                'date'=>date_format($object->getDateCreated(),"j F Y"),
             ];
         }

        if($object instanceof UserSetting){
            return [
                'id'=>$object->getId(),
                'phone'=>$object->getNotifications(),
                'account'=>$object->getAccount(),
                'sms'=>$object->getSms(),
                'email'=>$object->getEmail(),
             ];
         }      
         
        if($object instanceof InformationCenter){
            return [
                'title'=>$object->getHeader(),
                'body'=>$object->getBody(),
                'sort'=>$object->getList()
             ];
         }

        return [];
    }     
    public function getInformation(string $type):array{
        $data = [];
            $items = $this->em->getRepository(InformationCenter::class)->findBy(['section'=> $type]);
            $data = $this->Objects($items);
            $key_values = array_column($data, 'sort'); 
            array_multisort($key_values, SORT_ASC, $data);
         return $data;    
     }


     public   function Objects($items=[]):array{
        $processedItems = [];
           foreach ($items as $item ){
                   array_push($processedItems,$this->sortObjectToArray($item));
           }
       return $processedItems;
    }     
    public function getAppSettings():array{
        $data = [];
        $settings = $this->em->getRepository(AppSettings::class)->findAll();
        foreach($settings as $setting){
            $data[strtolower($setting->getName())] = !empty($setting->getCustomValue())?$setting->getCustomValue():$setting->getDefaultValue();
        }
       return $data;
    }
    public function getNotifications(UserAdmin $user):array{
        return $this->sortObjectToArray($user->getNotifications());    
    }
    public function getNotificationState(AppUserNotification $notification):bool{
       $state = false;
         $item = $this->em->getRepository(UserAppNotification::class)->findOneBy(['appNotification'=> $notification]);
       if($item){
       $state =  $item->getNotificationRead();
       }
       
        return $state;    
    }
    public function delete($class,$id):bool{
        $delete = false;
        $item = $this->em->getRepository($class)->find($id);
        if($item){
            $this->em->remove($item);
            $this->em->flush();
            $delete = true;
            }
        return $delete;
    }

    public function getDistance($origin,$destination){     
            $time = 0;
            $distance = 0;
            $apiKey= $this->params->get("GOOGLE_MAP_KEY");  
            if($apiKey) {
                $data = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($origin)."&destinations=".urlencode($destination)."&key=$apiKey&language=en-EN&sensor=false";
                $cURLConnection = curl_init();
                curl_setopt($cURLConnection, CURLOPT_URL, $data);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($cURLConnection);
                curl_close($cURLConnection);
                $data = json_decode($result);
                if($data->status=="OK") {
                    foreach($data->rows[0]->elements as $road) {
                        if(isset($road->duration)){
                            $time += $road->duration->value;
                        }
                        if(isset($road->distance)){
                            $distance += $road->distance->value;
                        }                       
                        
                    }
                    $destination = $data->destination_addresses[0];
                    $from = $data->origin_addresses[0];
                    $time = ($time/60);
                    $distance = ($distance/1000);
                }
            }

        return [
            'from'=>$origin,
            'to'=>$destination,
            'time'=>$time,
            'distance'=>$distance
        ];
      
    }

    public function addMandatoryLevies(UserAdmin $user){
        $levies = $this->em->getRepository(Levy::class)->findBy(['active'=>1,'mandatory'=>1]);
        foreach($levies as $levy){
            $userAlreadyHaveLevyApplied = $this->em->getRepository(UserLevy::class)->findOneBy(['useradmin'=>$user->getId(),'levy'=>$levy->getId()]);
            if(!$userAlreadyHaveLevyApplied){
                $instance = new UserLevy();           
                $instance->setUserAdmin($user->getId());
                $instance->setLevy($levy->getId());
                $instance->setActive(true);
                $this->em->persist($instance);
                $this->em->flush();
            }
        }  
    }

    public function getLevies(UserAdmin $user){
        $mandatory = $this->em->getRepository(Levy::class)->findBy(['active'=>1,'mandatory'=>1]);        
        $userLevies =  $user->getLevies();   
        return Validate::array_merge([$mandatory,$userLevies]); 
    }

    public function getRentalLevies($id){
        $levyList = [];
        $rentalLevies = $this->em->getRepository(RentalLevy::class)->findBy(['rental'=>$id]);
            foreach($rentalLevies as $item){
                 $levy = $this->em->getRepository(Levy::class)->find($item->getLevy());
                    if($levy){
                        $obj = $this->sortObjectToArray($levy);
                        $obj['total'] = $item->getTotal();
                        $obj['display']= '';
                        array_push($levyList,$obj);
                    }
                }
        return $levyList;
    }

    public function getRentalDiscounts($id){
        $discountList = [];
        $rentalLevies = $this->em->getRepository(RentalDiscount::class)->findBy(['rental'=>$id]);
            foreach($rentalLevies as $item){
                 $discount = $this->em->getRepository(Promotion::class)->find($item->getDiscount());
                    if($discount){
                        $obj = $this->sortObjectToArray($discount);
                        $obj['total'] = $item->getTotal();
                        $obj['display']= $discount->getAmount();
                        array_push($discountList,$obj);
                    }
                }
        return $discountList;
    }

    
  }