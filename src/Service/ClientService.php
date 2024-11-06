<?php 
namespace App\Service;

use App\Entity\Enum\AccountTypeEnum;
use App\Entity\Enum\RentalEnum;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rental;
use App\Entity\UserAdmin;

class ClientService{
    
    private $em;
    private $notification;
    private $apiService;
    private $dashboardService;

    public function __construct(EntityManagerInterface $em ,Notification $notification,ApiService $apiService,DashboardService $dashboardService)
    {
        $this->em = $em;
        $this->notification = $notification;
        $this->apiService =  $apiService;
        $this->dashboardService = $dashboardService;
    }

    public function getDashboadCounts(UserAdmin $user){
        $items = [];
        $appData = $this->dashboardService->getSiteDetails();
            if($user->getType()==AccountTypeEnum::RENTEE) {
                $cars = $user->getCars();
                $booked = 0;
                $active = 0;
                foreach($cars as $car) {
                    if($car->getBooked()) {
                        $booked++;
                    }
                    if($car->getActive()) {
                        $active++;
                    }
                }
                array_push(
                    $items,
                    [
                    "title"=>"Cars",
                    "count"=>count($cars),
                    'link'=>"/dashboard/cars",
                    "subs"=>[
                        "Booked"=>$booked,
                        "Active"=>$active,
                    ]]
                );
            }
            
            $rentals = $user->getRentals();
            $total = 0;
            $totalWithoutDeposit = 0;
            $inProgress = 0;
            $done = 0;
            foreach($rentals as $rental) {
                $total = $total + $rental->getQuoteAmount();
                $totalWithoutDeposit =$totalWithoutDeposit + ($rental->getQuoteAmount() - $rental->getCar()->getRefundableDeposit());
                if($rental->getStatus() == RentalEnum::INPROGRESS) {
                    $inProgress++;
                }
                if($rental->getStatus() == RentalEnum::DONE) {
                    $done++;
                }
            }
           $item =  [ 
                "title"=>"Rentals",
                "count"=>count($rentals),
                "link"=>"/dashboard/rentals",
                "subs"=>[
                    "Total"=>$appData['currency'].$total,
                    "In process"=>$inProgress,
                    "Done"=>$done
                ]];

                if($user->getType()==AccountTypeEnum::RENTEE) {
                    $item['subs']["Total less refundable deposits"] = $appData['currency'].$totalWithoutDeposit;
                }            
            array_push($items,$item);

        return $items;
    }



    public function accountUpdates(){       
        $count = 0;
        $rentals = $this->em->getRepository(Rental::class)->findAll();
        $currentDate = new \DateTime('now');        
        foreach ($rentals as $rental) {
            $user = $rental->getUser();
            $settings = $user->getUserSetting();
            if($rental->getStatus()==RentalEnum::WAITINGPAYMENT){
                $pickupdate = Validate::sortDate($rental->getPickupdate());            
                $daysToBooking = $pickupdate->diff($currentDate);
                $daysTillBookedDate = $daysToBooking->d; 
                if($daysTillBookedDate>=0){
                    //Notify user for the paying payment
                    if($settings->getEmail()){
                        $this->notification->sendEmail([
                            "from"=>$this->apiService->getAppSettings()['email'],
                            "to"=>$user->getEmail(),
                            "replyTo"=>$this->apiService->getAppSettings()['email'],
                            "subject"=>"Rental #".$rental->getId()." Payment POP required",
                            "text"=>"Rental #".$rental->getId()." Payment POP required",
                            "template"=>"",
                            "context"=>[
                                'message'=>'Make sure you submit the POP before the day of your rental stating on '.date_format($pickupdate,'j F Y h:m')
                                ]
                        ]);
                    }
                    if($settings->getSms()){
                        // if($user->getPhoneVerified()){
                        //     $this->notification->sendSms(['phone'=>$user->getPhone(),'message'=> "Rental #".$rental->getId()." Payment POP required"]);
                        // }
                    }
                    if($settings->getNotification()){
                        $this->notification->sendNotification($user,[
                            'header'=>"Rental #".$rental->getId()." Payment POP required",
                            'body'=>'Make sure you submit the POP before the day of your rental stating on '.date_format($pickupdate,'j F Y h:m')
                        ]);
                    }
                }
            }
            if($rental->getStatus()==RentalEnum::INPROGRESS){
                $returndate = Validate::sortDate($rental->getDropoffdate());           
                $daysRemaining = $returndate->diff($currentDate);
                $daysRemainingTillBookingEnd = $daysRemaining->d; 
                if($daysRemainingTillBookingEnd >=0){
                    //Notify user for of the remaining days
                    if($settings->getEmail()){
                        $this->notification->sendEmail([
                            "from"=>$this->apiService->getAppSettings()['email'],
                            "to"=>$user->getEmail(),
                            "replyTo"=>$this->apiService->getAppSettings()['email'],
                            "subject"=>"Rental #".$rental->getId()." coming to an end soon",
                            "text"=>"Rental #".$rental->getId()." coming to an end soon",
                            "template"=>"",
                            "context"=>[
                                'message'=>'Make sure you return the vehicle on time to avoid penalties! Rental ending on '.date_format($returndate,'j F Y h:m')
                                ]
                        ]);
                    }
                    if($settings->getSms()){
                        if($user->getPhoneVerified()){
                            $this->notification->sendSms(['phone'=>$user->getPhone(),'message'=> 'Make sure you return the vehicle on time to avoid penalties! Rental ending on '.date_format($returndate,'j F Y h:m')]);
                        }
                    }
                    if($settings->getNotification()){
                        $this->notification->sendNotification($user,[
                            'header'=>"Rental #".$rental->getId()." coming to an end soon",
                            'body'=>'Make sure you return the vehicle on time to avoid penalties! Rental ending on '.date_format($returndate,'j F Y h:m')
                        ]);
                    }
                }
            }
              $count++;              
            }
        
        return $count;
    }


    public function totalBreakDown(Rental $rental){
            $pickupdate = $rental->getPickupdate();
            $returndate = $rental->getDropoffdate();
            $daysBooked = 0;
            $vehicleRate = 0;
            $additionItems = [];
            $totalBookingFee = 0 ;
        if($pickupdate && $returndate){
            $pickupdate = Validate::sortDate($pickupdate);
            $returndate = Validate::sortDate($returndate); 
            $bookingDays = $pickupdate->diff($returndate);   
            $daysBooked = $bookingDays->d;
            if($daysBooked<=0){
                $daysBooked = 1;
            }
       }

        $additionals = $rental->getCar()->getOwner()->getUserCarAdditionals();
        $additionalTotalToAdd = 0;
        foreach($additionals as $additional){
            if($additional->getAddToBookingtotal()){
                $additionalTotalToAdd = $additionalTotalToAdd + $additional->getAmount();  
                array_push($additionItems,['description'=>$additional->getDescription(),'amount'=>$additional->getAmount()]);                      
            }
            
        }

            $car = $this->apiService->sortObjectToArray($rental->getCar());
            if($car["rate"]){
                $totalBookingFee = $daysBooked*$car["rate"];
                $totalBookingFee = $totalBookingFee+$car['deposit']+$additionalTotalToAdd;
            }

          return [ 
            "rate"=>$car["rate"],
            "deposit"=>$car['deposit'],
            'beforeDeposit'=> $daysBooked*$car["rate"],
            "additionItems"=>$additionItems,
            "daysBooked"=>$daysBooked,
            "totalBookingFee"=>$totalBookingFee
        ];
    }

}