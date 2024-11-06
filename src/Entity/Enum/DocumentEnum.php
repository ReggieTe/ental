<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class DocumentEnum extends ReadableEnum
{
    
    const ID  = "ID";
    const PASSPORT = "Passport";
    const LICENSE = "License";
    const PROOFORADDRESS = "Proof of address";


    protected static $default = self::ID;

    public static function choices(): array
    {
        return [
            self::ID  => "id",
            self::PASSPORT => "passport",
            self::LICENSE => "license",
            self::PROOFORADDRESS => "proof of address",
        ];
    }

}