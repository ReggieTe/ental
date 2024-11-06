<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class PromotionTypeEnum extends ReadableEnum
{
    
    const AMOUNT  = "AMOUNT";
    const PECENTAGE = "PECENTAGE";
    const DAY = "DAY";


    protected static $default = self::PECENTAGE;

    public static function choices(): array
    {
        return [
            self::AMOUNT  => self::AMOUNT,
            self::PECENTAGE => self::PECENTAGE,
            self::DAY => self::DAY
        ];
    }

}