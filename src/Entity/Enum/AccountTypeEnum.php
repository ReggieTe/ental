<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class AccountTypeEnum extends ReadableEnum
{
    
    const RENTER = 'Renter';
    const RENTEE = 'Rentee';

    protected static $default = self::RENTER;

    public static function choices(): array
    {
        return [
            self::RENTER =>'Renter',
            self::RENTEE=> 'Rentee',
        ];
    }
}

