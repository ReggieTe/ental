<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class RentalEnum extends ReadableEnum
{
        
    const WAITINGPAYMENT = 'Waiting payment';
    const WAITINGCARCOLLECTION = 'Waiting car collection by client';
    const INPROGRESS = 'In progress';
    const DONE = "Done";


    protected static $default = self::WAITINGPAYMENT;

    public static function choices(): array
    {
        return [
            self::WAITINGPAYMENT =>'waiting-payment',
            self::WAITINGCARCOLLECTION => 'waiting-collection',
            self::INPROGRESS => 'in-progress',
            self::DONE=>'done'
        ];
    }
}