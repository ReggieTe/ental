<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class AgreedEnum extends ReadableEnum
{
    
    const YES = 'I agree';
    const NO = "I don't agree";


    protected static $default = self::YES ;

    public static function choices(): array
    {
        return [            
            self::YES => '1',
            self::NO => '2',
        ];
    }
}