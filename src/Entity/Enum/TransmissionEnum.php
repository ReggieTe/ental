<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class TransmissionEnum extends ReadableEnum
{
    
    const MANUAL = 'Manual';
    const AUTOMATIC = 'Automatic';


    protected static $default = self::AUTOMATIC;

    public static function choices(): array
    {
        return [
            self::AUTOMATIC => self::AUTOMATIC,
            self::MANUAL => self::MANUAL,
        ];
    }
}