<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class FuelEnum extends ReadableEnum
{
    
    const PETROL = 'Petrol';
    const DIESEL = 'Diesel';


    protected static $default = self::PETROL;

    public static function choices(): array
    {
        return [
            self::PETROL =>self::PETROL,
            self::DIESEL => self::DIESEL,
        ];
    }
}