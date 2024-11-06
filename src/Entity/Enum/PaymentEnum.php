<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class PaymentEnum extends ReadableEnum
{
        
    const PAYFAST = 'Payfast';
    const PAYPAL = 'Paypal';
    const EFT = 'EFT';
    const CASH = 'CASH';
   // const UNKNOWN = 'Unknown';


   // protected static $default = self::UNKNOWN;

    public static function choices(): array
    {
        return [
           // self::UNKNOWN=>'unknown',
            self::EFT =>self::EFT,
            self::CASH =>self::CASH,
            self::PAYFAST => self::PAYFAST,
            self::PAYPAL => self::PAYPAL,
        ];
    }
}