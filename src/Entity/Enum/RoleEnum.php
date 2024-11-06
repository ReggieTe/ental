<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class RoleEnum extends ReadableEnum
{
    
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_USER';

    protected static $default = self::ROLE_USER;

    public static function choices(): array
    {
        return [
            self::ROLE_USER => 'ROLE_USER',
            self::ROLE_ADMIN => 'ROLE_ADMIN',
        ];
    }
}