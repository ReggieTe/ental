<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class PaymentStatusEnum extends ReadableEnum
{
    
    const PROCESSING = 'Processing';
    const PENDING = 'Pending';
    const SUCCESSFUL = 'Successful';
    const DONE = 'Done';
    const REJECTED = 'Rejected';
    const UNKNOWN = 'Unknown';


    protected static $default = self::UNKNOWN;

    public static function choices(): array
    {
        return [
            self::PROCESSING => 'processing',
            self::PENDING => 'pending',
            self::SUCCESSFUL => 'successful',
            self::DONE => 'done',
            self::REJECTED => 'rejected',
            self::UNKNOWN =>'unknown',
        ];
    }
}