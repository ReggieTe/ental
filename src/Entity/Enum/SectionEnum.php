<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class SectionEnum extends ReadableEnum
{
    
    const TERMS = 'Terms & Conditions';
    const DISCLAIMER = "Disclaimer";
    const FAQS = "FAQs";
    const PRIVACY = "Privacy Policy";

    protected static $default = self::TERMS;

    public static function choices(): array
    {
        return [
            self::TERMS => self::TERMS,
            self::DISCLAIMER => self::DISCLAIMER,
            self::FAQS => self::FAQS,
            self::PRIVACY => self::PRIVACY,
        ];
    }

    public static function items(): array
    {
        return [
            self::TERMS,
            self::DISCLAIMER,
            self::FAQS,
            self::PRIVACY,
        ];
    }
}

