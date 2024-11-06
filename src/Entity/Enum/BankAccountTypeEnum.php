<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class BankAccountTypeEnum extends ReadableEnum
{
    
    const CURRENT  = "CURRENT";
    const CHEQUE = "CHEQUE";
    const CREDIT = "CREDIT";


    protected static $default = self::CHEQUE;

    public static function choices(): array
    {
        return [
            self::CURRENT  => self::CURRENT,
            self::CHEQUE => self::CHEQUE,
            self::CREDIT => self::CREDIT,
        ];
    }

}