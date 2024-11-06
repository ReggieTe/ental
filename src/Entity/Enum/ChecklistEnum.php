<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class ChecklistEnum extends ReadableEnum
{
    
    const BEGINNING = 'BEGINNING OF THE RESERVATION';
    const END = 'END OF THE RESERVATION';


    protected static $default = self::BEGINNING;

    public static function choices(): array
    {
        return [
            self::BEGINNING => 'beginning',
            self::END => 'end',
        ];
    }
}